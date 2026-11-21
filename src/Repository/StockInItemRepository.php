<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\StockInItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StockInItem>
 */
class StockInItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StockInItem::class);
    }
}
