<?php
namespace App\Repository\Interfaces;

use App\Entity\Order;

interface OrderRepositoryInterface
{
    public function find(int $id): ?Order;
    /**
     * @return Order[]
     */
    public function findAll(): array;
    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return Order[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Order;
    public function save(Order $order): void;
    public function delete(Order $order): void;
}