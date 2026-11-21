<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DebtPayment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DebtPayment>
 */
class DebtPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtPayment::class);
    }
}
