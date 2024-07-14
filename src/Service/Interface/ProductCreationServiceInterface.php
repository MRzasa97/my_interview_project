<?php
namespace App\Service\Interface;

use App\Entity\Product;
use Brick\Money\Money;

interface ProductCreationServiceInterface
{
    public function createProduct(string $name, Money $price): Product;
}