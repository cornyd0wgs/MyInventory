<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Table(name: 'sale', indexes: [
    new ORM\Index(name: 'idx_sale_created_at', columns: ['created_at']),
])]
class Sale
{
    public const PAYMENT_CASH = 'CASH';
    public const PAYMENT_UTANG = 'UTANG';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_VOIDED = 'VOIDED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private string $invoiceNumber = '';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $cashier = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'sales')]
    private ?Customer $customer = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $amountTendered = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $changeAmount = '0.00';

    #[ORM\Column(length: 10)]
    private string $paymentType = self::PAYMENT_CASH;

    #[ORM\Column(length: 12)]
    private string $status = self::STATUS_COMPLETED;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $voidReason = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $voidedBy = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $voidedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, SaleItem> */
    #[ORM\OneToMany(mappedBy: 'sale', targetEntity: SaleItem::class, cascade: ['persist'])]
    private Collection $items;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(string $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getCashier(): ?User
    {
        return $this->cashier;
    }

    public function setCashier(?User $cashier): static
    {
        $this->cashier = $cashier;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getTotal(): string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getAmountTendered(): string
    {
        return $this->amountTendered;
    }

    public function setAmountTendered(string $amountTendered): static
    {
        $this->amountTendered = $amountTendered;

        return $this;
    }

    public function getChangeAmount(): string
    {
        return $this->changeAmount;
    }

    public function setChangeAmount(string $changeAmount): static
    {
        $this->changeAmount = $changeAmount;

        return $this;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): static
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getVoidReason(): ?string
    {
        return $this->voidReason;
    }

    public function setVoidReason(?string $voidReason): static
    {
        $this->voidReason = $voidReason;

        return $this;
    }

    public function getVoidedBy(): ?User
    {
        return $this->voidedBy;
    }

    public function setVoidedBy(?User $voidedBy): static
    {
        $this->voidedBy = $voidedBy;

        return $this;
    }

    public function getVoidedAt(): ?\DateTimeImmutable
    {
        return $this->voidedAt;
    }

    public function setVoidedAt(?\DateTimeImmutable $voidedAt): static
    {
        $this->voidedAt = $voidedAt;

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

    /** @return Collection<int, SaleItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(SaleItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setSale($this);
        }

        return $this;
    }

    public function removeItem(SaleItem $item): static
    {
        if ($this->items->removeElement($item) && $item->getSale() === $this) {
            $item->setSale(null);
        }

        return $this;
    }
}
