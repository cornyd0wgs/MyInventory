<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\StockInRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StockInRepository::class)]
class StockIn
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private string $referenceNumber = '';

    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'stockIns')]
    private ?Supplier $supplier = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receivedBy = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalCost = '0.00';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $receivedAt;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /** @var Collection<int, StockInItem> */
    #[Assert\Count(min: 1, minMessage: 'Add at least one item.')]
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'stockIn', targetEntity: StockInItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->receivedAt = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Manila'));
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): static
    {
        $this->referenceNumber = $referenceNumber;

        return $this;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function setReceivedBy(?User $receivedBy): static
    {
        $this->receivedBy = $receivedBy;

        return $this;
    }

    public function getTotalCost(): string
    {
        return $this->totalCost;
    }

    public function setTotalCost(string $totalCost): static
    {
        $this->totalCost = $totalCost;

        return $this;
    }

    public function getReceivedAt(): \DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(\DateTimeImmutable $receivedAt): static
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    /** @return Collection<int, StockInItem> */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(StockInItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setStockIn($this);
        }

        return $this;
    }

    public function removeItem(StockInItem $item): static
    {
        if ($this->items->removeElement($item) && $item->getStockIn() === $this) {
            $item->setStockIn(null);
        }

        return $this;
    }
}
