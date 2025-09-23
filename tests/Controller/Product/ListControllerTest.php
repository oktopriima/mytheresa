<?php

namespace App\Tests\Controller\Product;

use App\Controller\Product\ListController;
use App\Repository\PriceRulesRepository;
use App\Repository\ProductRepository;
use App\Request\Product\ListRequest;
use App\Services\Product\ListServices;
use App\Tests\Database\Mock\PriceRuleMock;
use App\Tests\Database\Mock\ProductMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ListControllerTest extends TestCase
{
    protected ProductRepository $productRepository;
    protected PriceRulesRepository $priceRulesRepository;
    protected DenormalizerInterface $denormalizer;
    protected ValidatorInterface $validator;

    public function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->priceRulesRepository = $this->createMock(PriceRulesRepository::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testIndexWithFailedParamValidation(): void
    {
        $minPrice = 1000000;
        $page = 0;
        $limit = 100;

        $dto = new ListRequest();
        $dto->setPriceLessThan($minPrice);
        $dto->setPage($page);
        $dto->setLimit($limit);

        $this->denormalizer->method('denormalize')
            ->willReturn($dto);

        $req = new Request();
        $req->query->set('priceLessThan', $minPrice);
        $req->query->set('page', $page);
        $req->query->set('limit', $limit);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $controller = new ListController();

        $response = $controller->index($req, $service);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertFalse($content["status"]);
        $this->assertStringContainsString("Error validate the request", $content["message"]);
        $this->assertContains("Price must be less than 1.000.000", $content["error"]);
        $this->assertContains("Page must be greater than 0", $content["error"]);
        $this->assertContains("Maximum limit is 20", $content["error"]);
    }

    public function testIndexWithSuccessParamValidation(): void
    {
        $minPrice = 100;
        $page = 1;
        $limit = 20;
        $dto = new ListRequest();
        $dto->setPriceLessThan($minPrice);
        $dto->setPage($page);
        $dto->setLimit($limit);

        $this->denormalizer->method('denormalize')
            ->willReturn($dto);

        $req = new Request();
        $req->query->set('priceLessThan', $minPrice);
        $req->query->set('page', $page);
        $req->query->set('limit', $limit);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $controller = new ListController();

        $response = $controller->index($req, $service);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($content["status"]);
        $this->assertIsArray($content);
    }

    public function testIndexWithPriceRulesImplementations(): void
    {
        $dto = new ListRequest();
        $products = ProductMock::multiple();
        $this->productRepository->method('findByParams')
            ->with($dto)
            ->willReturn($products);

        $priceRules = PriceRuleMock::multiple();
        $this->priceRulesRepository->method('findByIsActive')
            ->willReturn($priceRules);

        $this->denormalizer->method('denormalize')
            ->willReturn($dto);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $controller = new ListController();

        $req = new Request();
        $response = $controller->index($req, $service);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertTrue($content["status"]);
        $this->assertSameSize($products, $content["data"]);

        // check top 5 data
        for ($i = 0; $i < 5; $i++) {
            $this->assertContains($products[$i]->getName(), $content["data"][$i]);
        }

        // check discount calculated on double impact discount
        // make sure applied on the biggest one
        $impactedSKU = "000003";
        $x = null;
        foreach ($products as $product) {
            if ($product->getSKU() === $impactedSKU) {
                $x = $product;
                break;
            }
        }
        $this->assertNotNull($x);
        $expectedDiscountAmount = ($x->gePrice() * 30) / 100;
        $expectedFinalPrice = $x->gePrice() - $expectedDiscountAmount;

        $y = [];
        foreach ($content["data"] as $item) {
            if ($item["sku"] === $impactedSKU) {
                $y = $item;
                break;
            }
        }
        $this->assertNotEmpty($y);

        $this->assertEquals($expectedFinalPrice, $y["price"]["final"]);
        $this->assertEquals($x->gePrice(), $y["price"]["original"]);
        $this->assertEquals("30%", $y["price"]["discount_percentage"]);
    }
}
