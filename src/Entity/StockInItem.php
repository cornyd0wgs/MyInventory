<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StockInItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: StockInItemRepository::class)]
class StockInItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: StockIn::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StockIn $stockIn = null;

    #[Assert\NotNull(message: 'Choose a product.')]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[Assert\GreaterThan(0, message: 'Quantity must be greater than 0.')]
    #[ORM\Column]
    private int $quantity = 0;

    #[Assert\NotBlank(message: 'Enter a unit cost.')]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $unitCost = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $subtotal = '0.00';

    #[Assert\Callback]
    public function validateUnitCost(ExecutionContextInterface $context): void
    {
        if (preg_match('/^\d+(\.\d{1,2})?$/', $this->unitCost) !== 1) {
            $context->buildViolation('Unit cost must be a decimal amount with at most 2 decimal places.')
                ->atPath('unitCost')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStockIn(): ?StockIn
    {
        return $this->stockIn;
    }

    public function setStockIn(?StockIn $stockIn): static
    {
        $this->stockIn = $stockIn;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitCost(): string
    {
        return $this->unitCost;
    }

    public function setUnitCost(string $unitCost): static
    {
        $this->unitCost = $unitCost;

        return $this;
    }

    public function getSubtotal(): string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): static
    {
        $this->subtotal = $subtotal;

        return $this;
    }
}
