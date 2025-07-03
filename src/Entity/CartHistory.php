<?php

namespace App\Entity;

use App\Repository\CartHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartHistoryRepository::class)]
class CartHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ProductName = null;

    #[ORM\Column(length: 255)]
    private ?string $ProductPrice = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $subTotal = null;

    #[ORM\Column(length: 255)]
    private ?string $orderReference = null;

    #[ORM\ManyToOne(targetEntity: Order::class ,inversedBy: 'cartHistories', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->ProductName;
    }

    public function setProductName(string $ProductName): static
    {
        $this->ProductName = $ProductName;

        return $this;
    }

    public function getProductPrice(): ?string
    {
        return $this->ProductPrice;
    }

    public function setProductPrice(string $ProductPrice): static
    {
        $this->ProductPrice = $ProductPrice;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSubTotal(): ?float
    {
        return $this->subTotal;
    }

    public function setSubTotal(float $subTotal): static
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getOrderReference(): ?string
    {
        return $this->orderReference;
    }

    public function setOrderReference(string $orderReference): static
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }
}
