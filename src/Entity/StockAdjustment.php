<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StockAdjustmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockAdjustmentRepository::class)]
class StockAdjustment
{
    public const TYPE_DAMAGED = 'DAMAGED';
    public const TYPE_EXPIRED = 'EXPIRED';
    public const TYPE_LOST = 'LOST';
    public const TYPE_CORRECTION = 'CORRECTION';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $adjustedBy = null;

    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_CORRECTION;

    #[ORM\Column]
    private int $quantityChange = 0;

    #[ORM\Column(type: 'text')]
    private string $reason = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdjustedBy(): ?User
    {
        return $this->adjustedBy;
    }

    public function setAdjustedBy(?User $adjustedBy): static
    {
        $this->adjustedBy = $adjustedBy;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantityChange(): int
    {
        return $this->quantityChange;
    }

    public function setQuantityChange(int $quantityChange): static
    {
        $this->quantityChange = $quantityChange;

        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
