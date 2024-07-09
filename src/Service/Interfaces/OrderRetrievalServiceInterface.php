<?php

namespace App\Service\Interfaces;

use App\Entity\Order;

interface OrderRetrievalServiceInterface
{
    public function getOrder(int $orderId): ?Order;
}