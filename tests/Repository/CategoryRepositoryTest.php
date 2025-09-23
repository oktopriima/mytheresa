<?php

namespace App\Tests\Repository;

use App\DataFixtures\ProductFixtures;
use App\Repository\CategoriesRepository;

class CategoryRepositoryTest extends RepositorySetup
{
    protected CategoriesRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->get(CategoriesRepository::class);
        (new ProductFixtures())->load($this->em);
    }

    public function testFindOneById()
    {
        $id = 1;
        $result = $this->repository->findOneById($id);

        $this->assertNotEmpty($result);
        $this->assertequals($result->getId(), $id);
    }

    public function testFailedFindOneById()
    {
        $result = $this->repository->findOneById(100);
        $this->assertEmpty($result);
    }

    public function testFindByName()
    {
        $name = "boots";
        $result = $this->repository->findOneByName($name);

        $this->assertNotEmpty($result);
        $this->assertequals($result->getName(), $name);
    }

    public function testFailedFindOneByName()
    {
        $result = $this->repository->findOneByName("laptop");
        $this->assertEmpty($result);
    }
}
