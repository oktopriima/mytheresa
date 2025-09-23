<?php

namespace App\Tests\Controller\Product;

use App\Controller\Product\ListController;
use App\Repository\PriceRulesRepository;
use App\Repository\ProductRepository;
use App\Request\Product\ListRequest;
use App\Services\Product\ListServices;
use App\Tests\Controller\Database\Mock\PriceRuleMock;
use App\Tests\Controller\Database\Mock\ProductMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validation;

final class ListControllerTest extends TestCase
{
    public function testIndexWithFailedParamValidation(): void
    {
        $minPrice = 1000000;
        $page = 0;
        $limit = 100;

        $dto = new ListRequest();
        $dto->setPriceLessThan($minPrice);
        $dto->setPage($page);
        $dto->setLimit($limit);

        $productRepo = $this->createMock(ProductRepository::class);
        $priceRuleRepo = $this->createMock(PriceRulesRepository::class);

        $denormalize = $this->createMock(DenormalizerInterface::class);
        $denormalize->method('denormalize')
            ->willReturn($dto);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $req = new Request();
        $req->query->set('priceLessThan', $minPrice);
        $req->query->set('page', $page);
        $req->query->set('limit', $limit);

        $service = new ListServices($productRepo, $priceRuleRepo, $denormalize, $validator);
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

        $productRepo = $this->createMock(ProductRepository::class);
        $priceRuleRepo = $this->createMock(PriceRulesRepository::class);
        $denormalize = $this->createMock(DenormalizerInterface::class);
        $denormalize->method('denormalize')
            ->willReturn($dto);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $req = new Request();
        $req->query->set('priceLessThan', $minPrice);
        $req->query->set('page', $page);
        $req->query->set('limit', $limit);

        $service = new ListServices($productRepo, $priceRuleRepo, $denormalize, $validator);
        $controller = new ListController();

        $response = $controller->index($req, $service);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($content["status"]);
        $this->assertIsArray($content);
    }

    public function testIndexWithPriceRulesImplementations(): void
    {
        $products = ProductMock::multiple();
        $productRepo = $this->createMock(ProductRepository::class);
        $productRepo->method('findByParams')
            ->with((function () {
                return new ListRequest();
            })())
            ->willReturn($products);

        $priceRules = PriceRuleMock::multiple();
        $priceRuleRepo = $this->createMock(PriceRulesRepository::class);
        $priceRuleRepo->method('findByIsActive')
            ->willReturn($priceRules);

        $denormalize = $this->createMock(DenormalizerInterface::class);
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $denormalize->method('denormalize')
            ->willReturn((function () {
                return new ListRequest();
            })());

        $service = new ListServices($productRepo, $priceRuleRepo, $denormalize, $validator);
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
