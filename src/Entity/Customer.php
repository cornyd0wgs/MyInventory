<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $name = '';

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactNumber = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $creditLimit = '1000.00';

    #[ORM\Column]
    private bool $isArchived = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, Sale> */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Sale::class)]
    private Collection $sales;

    /** @var Collection<int, DebtPayment> */
    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: DebtPayment::class)]
    private Collection $debtPayments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->sales = new ArrayCollection();
        $this->debtPayments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContactNumber(): ?string
    {
        return $this->contactNumber;
    }

    public function setContactNumber(?string $contactNumber): static
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCreditLimit(): string
    {
        return $this->creditLimit;
    }

    public function setCreditLimit(string $creditLimit): static
    {
        $this->creditLimit = $creditLimit;

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

    /** @return Collection<int, Sale> */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): static
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setCustomer($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): static
    {
        if ($this->sales->removeElement($sale) && $sale->getCustomer() === $this) {
            $sale->setCustomer(null);
        }

        return $this;
    }

    /** @return Collection<int, DebtPayment> */
    public function getDebtPayments(): Collection
    {
        return $this->debtPayments;
    }

    public function addDebtPayment(DebtPayment $debtPayment): static
    {
        if (!$this->debtPayments->contains($debtPayment)) {
            $this->debtPayments->add($debtPayment);
            $debtPayment->setCustomer($this);
        }

        return $this;
    }

    public function removeDebtPayment(DebtPayment $debtPayment): static
    {
        if ($this->debtPayments->removeElement($debtPayment) && $debtPayment->getCustomer() === $this) {
            $debtPayment->setCustomer(null);
        }

        return $this;
    }
}
