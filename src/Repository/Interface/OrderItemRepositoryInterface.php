<?php
namespace App\Repository\Interface;

use App\Entity\OrderItem;
use Doctrine\DBAL\LockMode;

interface OrderItemRepositoryInterface
{
    public function find(int $id): ?OrderItem;
    
    /**
     * @return OrderItem[]
     */
    public function findAll(): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return OrderItem[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?OrderItem;
}