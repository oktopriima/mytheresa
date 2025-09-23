<?php

namespace App\Tests\Repository;

use App\DataFixtures\ProductFixtures;
use App\Repository\ProductRepository;
use App\Request\Product\ListRequest;

class ProductRepositoryTest extends RepositorySetup
{
    protected ProductRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->get(ProductRepository::class);
        (new ProductFixtures())->load($this->em);
    }

    public function testFindByParamsWithLimit(): void
    {
        $dto = new ListRequest();
        $dto->setPage(2);
        $dto->setLimit(10);
        $result = $this->repository->findByParams($dto);

        $this->assertNotEmpty($result);
        $this->assertCount(5, $result);
        foreach ($result as $product) {
            $this->assertNotEmpty($product->getCategories());
        }
    }

    public function testFindByParamsWithPriceLessThan(): void
    {
        $dto = new ListRequest();
        $dto->setPriceLessThan(100000);
        $result = $this->repository->findByParams($dto);

        $this->assertNotEmpty($result);
        $this->assertCount(11, $result);
        foreach ($result as $product) {
            $this->assertNotEmpty($product->getCategories());
        }
    }

    public function testFindByParamsWithCategory(): void
    {
        $dto = new ListRequest();
        $dto->setCategory("boots");
        $result = $this->repository->findByParams($dto);

        $this->assertNotEmpty($result);
        $this->assertCount(3, $result);
        foreach ($result as $product) {
            $this->assertNotEmpty($product->getCategories());
        }
    }

    public function testFindByParamsWithCategories(): void
    {
        $dto = new ListRequest();
        $dto->setCategories(["boots", "sandals"]);
        $result = $this->repository->findByParams($dto);

        $this->assertNotEmpty($result);
        $this->assertCount(4, $result);
        foreach ($result as $product) {
            $this->assertNotEmpty($product->getCategories());
        }
    }

    public function testFindByParamsEmptyResult(): void
    {
        $dto = new ListRequest();
        $dto->setPriceLessThan(10);
        $result = $this->repository->findByParams($dto);

        $this->assertEmpty($result);
        $this->assertCount(0, $result);
    }
}
