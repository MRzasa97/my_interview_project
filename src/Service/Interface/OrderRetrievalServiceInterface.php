<?php

namespace App\Service\Interface;

use App\Entity\Order;

interface OrderRetrievalServiceInterface
{
    public function getOrder(int $orderId): ?Order;
}