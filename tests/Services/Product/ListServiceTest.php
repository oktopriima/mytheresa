<?php

namespace App\Tests\Services\Product;

use App\Repository\PriceRulesRepository;
use App\Repository\ProductRepository;
use App\Request\Product\ListRequest;
use App\Services\Product\ListServices;
use App\Tests\Database\Mock\PriceRuleMock;
use App\Tests\Database\Mock\ProductMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ListServiceTest extends TestCase
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

    public function testValidParams(): void
    {
        $dto = new ListRequest();
        $dto->setPriceLessThan(100);
        $dto->setPage(1);
        $dto->setLimit(10);
        $dto->setCategory("boots");
        $dto->setCategories(["sandals", "pants"]);

        $this->denormalizer
            ->method('denormalize')
            ->willReturn($dto);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $output = $service->call($dto->toArray());

        $this->assertTrue($output->ok());
        $this->assertFalse($output->fail());
        $this->assertEquals(200, $output->httpCode());
        $this->assertIsArray($output->result());
    }

    public function testInvalidParams(): void
    {
        $dto = new ListRequest();
        $dto->setPriceLessThan(0);
        $dto->setPage(0);
        $dto->setLimit(21);

        $this->denormalizer
            ->method('denormalize')
            ->willReturn($dto);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $output = $service->call($dto->toArray());

        $this->assertTrue($output->fail());
        $this->assertFalse($output->ok());
        $this->assertEquals(400, $output->httpCode());
        $this->assertEquals("Error validate the request", $output->message());
        $this->assertContains("Price must be greater than 0", $output->result());
        $this->assertContains("Page must be greater than 0", $output->result());
        $this->assertContains("Maximum limit is 20", $output->result());
    }

    public function testSuccessWithPriceRules(): void
    {
        $dto = new ListRequest();
        $this->denormalizer
            ->method('denormalize')
            ->willReturn($dto);

        $expectedProduct = ProductMock::multiple();
        $this->productRepository->method('findByParams')->willReturn($expectedProduct);

        $expectedPriceRule = PriceRuleMock::multiple();
        $this->priceRulesRepository->method('findByIsActive')->willReturn($expectedPriceRule);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $output = $service->call($dto->toArray());

        $this->assertTrue($output->ok());
        $this->assertFalse($output->fail());
        $this->assertEquals(200, $output->httpCode());
        $this->assertIsArray($output->result());

        foreach ($output->result() as $item) {
            if ($item->price["discount_percentage"] != null) {
                $this->assertTrue($item->price["original"] != $item->price["final"]);
                $this->assertTrue($item->price["original"] > $item->price["final"]);
            } else {
                $this->assertTrue($item->price["original"] == $item->price["final"]);
            }
        }
    }

    public function testSuccessWithoutPriceRules(): void
    {
        $dto = new ListRequest();
        $this->denormalizer
            ->method('denormalize')
            ->willReturn($dto);

        $expectedProduct = ProductMock::multiple();
        $this->productRepository->method('findByParams')->willReturn($expectedProduct);

        $service = new ListServices($this->productRepository, $this->priceRulesRepository, $this->denormalizer, $this->validator);
        $output = $service->call($dto->toArray());

        $this->assertTrue($output->ok());
        $this->assertFalse($output->fail());
        $this->assertEquals(200, $output->httpCode());
        $this->assertIsArray($output->result());

        foreach ($output->result() as $item) {
            $this->assertTrue($item->price["original"] == $item->price["final"]);
            $this->assertNull($item->price["discount_percentage"]);
        }
    }
}
