<?php

namespace App\Tests\Repository;

use App\DataFixtures\PriceRuleFixtures;
use App\Repository\PriceRulesRepository;

class PriceRuleRepositoryTest extends RepositorySetup
{
    protected PriceRulesRepository $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->container->get(PriceRulesRepository::class);
        (new PriceRuleFixtures())->load($this->em);
    }

    public function testFindActive(): void
    {
        $result = $this->repository->findByIsActive();

        $this->assertNotEmpty($result);
        $this->assertCount(2, $result);

        foreach ($result as $priceRule) {
            $this->assertNotEmpty($priceRule->getConditions());
        }
    }
}
