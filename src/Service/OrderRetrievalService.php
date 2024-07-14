<?php

namespace App\Service;

use App\Entity\Order;
use App\Repository\Interface\OrderRepositoryInterface;
use App\Service\Interface\OrderRetrievalServiceInterface;

class OrderRetrievalService implements OrderRetrievalServiceInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    )
    {}

    public function getOrder(int $orderId): ?Order
    {
        return $this->orderRepository->find($orderId);
    }
}
