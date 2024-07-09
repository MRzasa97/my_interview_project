<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\OrderItem;
use Brick\Money\Money;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'The total price must be zero or positive number')]
    private int $totalPrice = 0;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'The total VAT must be zero or a positive number')]
    private int $vatPrice = 0;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'The total NET must be zero or a positive number')]
    private int $netPrice = 0;

    #[ORM\Column(type: 'string', length: 3)]
    #[Assert\NotBlank]
    private string $currency = "USD";

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTotalPrice(): Money
    {
        return Money::ofMinor($this->totalPrice, $this->currency);
    }

    public function setTotalPrice(Money $totalPrice): static
    {
        $this->totalPrice = $totalPrice->getMinorAmount()->toInt();
        $this->currency = $totalPrice->getCurrency()->getCurrencyCode();

        return $this;
    }

    public function getVatPrice(): Money
    {
        return Money::ofMinor($this->vatPrice, $this->currency);
    }

    public function setVatPrice(Money $vatPrice): static
    {
        $this->vatPrice = $vatPrice->getMinorAmount()->toInt();;

        return $this;
    }

    public function getNetPrice(): Money
    {
        return Money::ofMinor($this->netPrice, $this->currency);
    }

    public function setNetPrice(Money $netPrice): static
    {
        $this->netPrice = $netPrice->getMinorAmount()->toInt();
        return $this;
    }
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }
}
