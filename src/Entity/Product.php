<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Brick\Money\Money;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name should not be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    private string $name = '';

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero(message: 'The price must be zero or positive number')]
    private int $price = 0;

    #[ORM\Column(type: 'string', length: 3)]
    #[Assert\NotBlank(message: 'The currency should not be blank.')]
    #[Assert\Currency(message: 'The currency should be a valid ISO 4217 currency code.')]
    private string $currency = 'USD';

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

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

    public function getPrice(): Money
    {
        return Money::ofMinor($this->price, $this->currency);
    }

    public function setPrice(Money $price): static
    {
        $this->price = $price->getMinorAmount()->toInt();
        $this->currency = $price->getCurrency()->getCurrencyCode();
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
