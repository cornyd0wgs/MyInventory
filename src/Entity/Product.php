<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[UniqueEntity(
    fields: ['barcode'],
    message: 'This barcode is already used.',
    repositoryMethod: 'findByIncludingArchived',
    ignoreNull: true,
)]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'product', indexes: [
    new ORM\Index(name: 'idx_product_name', columns: ['name']),
], uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_product_barcode', columns: ['barcode']),
])]
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $barcode = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 150)]
    private string $name = '';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $costPrice = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $sellingPrice = '0.00';

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column]
    private int $quantity = 0;

    #[Assert\GreaterThanOrEqual(0)]
    #[ORM\Column]
    private int $reorderLevel = 5;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $expirationDate = null;

    #[ORM\Column]
    private bool $isArchived = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function refreshUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateMoney(ExecutionContextInterface $context): void
    {
        foreach (['costPrice' => 'Cost price', 'sellingPrice' => 'Selling price'] as $field => $label) {
            $value = $field === 'costPrice' ? $this->costPrice : $this->sellingPrice;

            if (preg_match('/^\d+(\.\d{1,2})?$/', $value) !== 1) {
                $context->buildViolation($label.' must be a decimal amount with at most 2 decimal places.')
                    ->atPath($field)
                    ->addViolation();
            } elseif (bccomp($value, '0', 2) <= 0) {
                $context->buildViolation($label.' must be greater than 0.')
                    ->atPath($field)
                    ->addViolation();
            }
        }
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (null === $this->expirationDate) {
            return false;
        }

        $today = new \DateTimeImmutable('today');

        return $this->expirationDate >= $today && $this->expirationDate <= $today->modify(sprintf('+%d days', $days));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCostPrice(): string
    {
        return $this->costPrice;
    }

    public function setCostPrice(string $costPrice): static
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    public function getSellingPrice(): string
    {
        return $this->sellingPrice;
    }

    public function setSellingPrice(string $sellingPrice): static
    {
        $this->sellingPrice = $sellingPrice;

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

    public function getReorderLevel(): int
    {
        return $this->reorderLevel;
    }

    public function setReorderLevel(int $reorderLevel): static
    {
        $this->reorderLevel = $reorderLevel;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function setIsArchived(bool $isArchived): static
    {
        $this->isArchived = $isArchived;

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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
