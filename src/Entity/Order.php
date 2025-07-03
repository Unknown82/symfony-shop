<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    private ?string $userEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $userAddress = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentStatus = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPending = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isShipped = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDelivered = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isProcessed = null;

    #[ORM\Column(length: 255)]
    private ?string $orderReference = null;

    /**
     * @var Collection<int, CartHistory>
     */
    #[ORM\OneToMany(targetEntity: CartHistory::class, mappedBy: 'productOrder')]
    private Collection $cartHistories;

    public function __construct()
    {
        $this->cartHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): static
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserAddress(): ?string
    {
        return $this->userAddress;
    }

    public function setUserAddress(string $userAddress): static
    {
        $this->userAddress = $userAddress;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function isPending(): ?bool
    {
        return $this->isPending;
    }

    public function setIsPending(?bool $isPending): static
    {
        $this->isPending = $isPending;

        return $this;
    }

    public function isShipped(): ?bool
    {
        return $this->isShipped;
    }

    public function setIsShipped(?bool $isShipped): static
    {
        $this->isShipped = $isShipped;

        return $this;
    }

    public function isDelivered(): ?bool
    {
        return $this->isDelivered;
    }

    public function setIsDelivered(?bool $isDelivered): static
    {
        $this->isDelivered = $isDelivered;

        return $this;
    }

    public function isProcessed(): ?bool
    {
        return $this->isProcessed;
    }

    public function setIsProcessed(?bool $isProcessed): static
    {
        $this->isProcessed = $isProcessed;

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

    /**
     * @return Collection<int, CartHistory>
     */
    public function getCartHistories(): Collection
    {
        return $this->cartHistories;
    }

    public function addCartHistory(CartHistory $cartHistory): static
    {
        if (!$this->cartHistories->contains($cartHistory)) {
            $this->cartHistories->add($cartHistory);
            $cartHistory->setProductOrder($this);
        }

        return $this;
    }

    public function removeCartHistory(CartHistory $cartHistory): static
    {
        if ($this->cartHistories->removeElement($cartHistory)) {
            // set the owning side to null (unless already changed)
            if ($cartHistory->getProductOrder() === $this) {
                $cartHistory->setProductOrder(null);
            }
        }

        return $this;
    }
}
