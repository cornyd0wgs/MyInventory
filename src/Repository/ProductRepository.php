<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
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

    public function find(mixed $id, LockMode|int|null $lockMode = LockMode::NONE, ?int $lockVersion = null): ?object
    {
        $product = parent::find($id, $lockMode, $lockVersion);

        return $product instanceof Product && !$product->isArchived() ? $product : null;
    }

    /** @return list<Product> */
    public function findAll(): array
    {
        return parent::findBy(['isArchived' => false], ['name' => 'ASC']);
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return list<Product>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy(
            $criteria + ['isArchived' => false],
            $orderBy ?? ['name' => 'ASC'],
            $limit,
            $offset
        );
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        return parent::findOneBy($criteria + ['isArchived' => false], $orderBy);
    }

    /**
     * Used by UniqueEntity so archived barcodes still count as taken.
     *
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     *
     * @return list<Product>
     */
    public function findByIncludingArchived(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    /** @return list<Product> */
    public function search(?string $search, ?int $categoryId): array
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('c')
            ->join('p.category', 'c')
            ->andWhere('p.isArchived = false');

        if (null !== $search && '' !== $search) {
            $qb->andWhere('p.name LIKE :search OR p.barcode = :barcode')
                ->setParameter('search', '%'.$search.'%')
                ->setParameter('barcode', $search);
        }

        if (null !== $categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        /** @var list<Product> $products */
        $products = $qb->orderBy('p.name', 'ASC')->getQuery()->getResult();

        return $products;
    }

    /** @return list<Product> */
    public function lowStock(): array
    {
        /** @var list<Product> $products */
        $products = $this->createQueryBuilder('p')
            ->addSelect('c')
            ->join('p.category', 'c')
            ->andWhere('p.isArchived = false')
            ->andWhere('p.quantity <= p.reorderLevel')
            ->orderBy('p.quantity', 'ASC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $products;
    }

    public function countLowStock(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.isArchived = false')
            ->andWhere('p.quantity <= p.reorderLevel')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
