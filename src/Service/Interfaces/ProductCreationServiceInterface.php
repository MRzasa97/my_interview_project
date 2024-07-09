<?php
namespace App\Service\Interfaces;

use App\Entity\Product;
use Brick\Money\Money;

interface ProductCreationServiceInterface
{
    public function createProduct(string $name, Money $price): Product;
}