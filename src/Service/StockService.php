<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Entity\StockIn;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

final readonly class StockService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function recordStockIn(StockIn $stockIn, bool $updateCostPrice = false): void
    {
        $total = '0.00';

        /** @var list<array{product: Product, quantity: int, unitCost: string}> $lines */
        $lines = [];

        foreach ($stockIn->getItems() as $item) {
            $product = $item->getProduct();
            if (!$product instanceof Product || null === $product->getId()) {
                throw new \InvalidArgumentException('Each stock-in item needs a saved product.');
            }

            $quantity = $item->getQuantity();
            if ($quantity <= 0) {
                throw new \InvalidArgumentException('Item quantities must be greater than zero.');
            }

            $unitCost = $this->normalizeMoney($item->getUnitCost());
            if (bccomp($unitCost, '0', 2) < 0) {
                throw new \InvalidArgumentException('Unit cost cannot be negative.');
            }

            $subtotal = bcmul((string) $quantity, $unitCost, 2);
            $item->setUnitCost($unitCost)->setSubtotal($subtotal);
            $total = bcadd($total, $subtotal, 2);

            $lines[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unitCost' => $unitCost,
            ];
        }

        if ([] === $lines) {
            throw new \InvalidArgumentException('A stock-in needs at least one item.');
        }

        $stockIn->setTotalCost($total);
        $updatedAt = (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');

        $this->entityManager->wrapInTransaction(function () use ($stockIn, $lines, $updateCostPrice, $updatedAt): void {
            $this->entityManager->persist($stockIn);
            foreach ($stockIn->getItems() as $item) {
                $this->entityManager->persist($item);
            }

            foreach ($lines as $line) {
                $sql = 'UPDATE product SET quantity = quantity + :quantity, updated_at = :updatedAt'
                    .($updateCostPrice ? ', cost_price = :unitCost' : '')
                    .' WHERE id = :id AND is_archived = 0';

                $parameters = [
                    'quantity' => $line['quantity'],
                    'updatedAt' => $updatedAt,
                    'id' => $line['product']->getId(),
                ];
                $types = [
                    'quantity' => Types::INTEGER,
                    'updatedAt' => Types::STRING,
                    'id' => Types::INTEGER,
                ];

                if ($updateCostPrice) {
                    $parameters['unitCost'] = $line['unitCost'];
                    $types['unitCost'] = Types::STRING;
                }

                $affected = $this->entityManager->getConnection()->executeStatement($sql, $parameters, $types);
                if (1 !== $affected) {
                    throw new \RuntimeException('A selected product is no longer available.');
                }
            }

            $this->entityManager->flush();
        });
    }

    private function normalizeMoney(string $value): string
    {
        $value = trim($value);
        if (preg_match('/^\d+(\.\d{1,2})?$/', $value) !== 1) {
            throw new \InvalidArgumentException('Unit cost must be a decimal amount with at most 2 decimal places.');
        }

        return bcadd($value, '0', 2);
    }
}
