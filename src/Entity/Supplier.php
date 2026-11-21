<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private string $name = '';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $contactPerson = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $contactNumber = null;

    /** @var Collection<int, StockIn> */
    #[ORM\OneToMany(mappedBy: 'supplier', targetEntity: StockIn::class)]
    private Collection $stockIns;

    public function __construct()
    {
        $this->stockIns = new ArrayCollection();
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

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function setContactPerson(?string $contactPerson): static
    {
        $this->contactPerson = $contactPerson;

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

    /** @return Collection<int, StockIn> */
    public function getStockIns(): Collection
    {
        return $this->stockIns;
    }

    public function addStockIn(StockIn $stockIn): static
    {
        if (!$this->stockIns->contains($stockIn)) {
            $this->stockIns->add($stockIn);
            $stockIn->setSupplier($this);
        }

        return $this;
    }

    public function removeStockIn(StockIn $stockIn): static
    {
        if ($this->stockIns->removeElement($stockIn) && $stockIn->getSupplier() === $this) {
            $stockIn->setSupplier(null);
        }

        return $this;
    }
}
