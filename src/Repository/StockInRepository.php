<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StockIn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockIn>
 */
class StockInRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockIn::class);
    }

    /** @return list<StockIn> */
    public function findAllWithDetails(): array
    {
        /** @var list<StockIn> $records */
        $records = $this->createQueryBuilder('s')
            ->addSelect('sup', 'u')
            ->leftJoin('s.supplier', 'sup')
            ->join('s.receivedBy', 'u')
            ->orderBy('s.receivedAt', 'DESC')
            ->addOrderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $records;
    }

    /**
     * @param list<int> $ids
     *
     * @return array<int, int>
     */
    public function itemCounts(array $ids): array
    {
        if ([] === $ids) {
            return [];
        }

        /** @var list<array{stock_in_id: int|string, item_count: int|string}> $rows */
        $rows = $this->createQueryBuilder('s')
            ->select('s.id AS stock_in_id', 'COUNT(i.id) AS item_count')
            ->join('s.items', 'i')
            ->andWhere('s.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->groupBy('s.id')
            ->getQuery()
            ->getArrayResult();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(int) $row['stock_in_id']] = (int) $row['item_count'];
        }

        return $counts;
    }

    public function findOneWithDetails(int $id): ?StockIn
    {
        /** @var StockIn|null $stockIn */
        $stockIn = $this->createQueryBuilder('s')
            ->addSelect('sup', 'u', 'i', 'p')
            ->leftJoin('s.supplier', 'sup')
            ->join('s.receivedBy', 'u')
            ->join('s.items', 'i')
            ->join('i.product', 'p')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $stockIn;
    }
}
