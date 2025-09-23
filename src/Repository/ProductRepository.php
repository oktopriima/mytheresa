<?php

namespace App\Repository;

use App\Entity\Product;
use App\Request\Product\ListRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByParams(ListRequest $dto): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($dto->getPriceLessThan() !== null) {
            $qb->andWhere('p.price < :priceLessThan')
                ->setParameter('priceLessThan', $dto->getPriceLessThan());
        }

        if ($dto->getCategory() !== null) {
            $qb->leftJoin('p.categories', 'c')
                ->andWhere('c.name = :category')
                ->setParameter('category', $dto->getCategory());
        }

        if ($dto->getCategories() !== null) {
            $qb->leftJoin('p.categories', 'c')
                ->andWhere('c.name IN (:categories)')
                ->setParameter('categories', $dto->getCategories());
        }

        $qb->setMaxResults($dto->getLimit() ?? 20)
            ->setFirstResult(((($dto->getPage() ?? 1) - 1) * ($dto->getLimit() ?? 20)));

        return $qb->getQuery()->getResult();
    }
}
