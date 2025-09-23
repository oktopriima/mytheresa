<?php

namespace App\Repository;

use App\Entity\PriceRules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRules>
 */
class PriceRulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRules::class);
    }

    /**
     * Return list of active price rules
     * @return PriceRules[] Returns an array of PriceRules objects
     */
    public function findByIsActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.is_active = :is_active')
            ->setParameter('is_active', true)
            ->getQuery()
            ->getResult();
    }
}
