<?php

namespace App\Service\Interfaces;

use App\Entity\Order;


interface OrderProcessingServiceInterface
{
    /**
    * @param array<array{productId: int, quantity: int}> $items
    */
    public function createOrder(array $items): Order;
}