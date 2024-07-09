<?php
namespace App\Repository\Interfaces;

use App\Entity\Product;

interface ProductRepositoryInterface
{
    public function find(int $id): ?Product;
    /**
     * @return Product[]
     */
    public function findAll(): array;
    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return Product[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;
    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Product;
    public function save(Product $product): void;
    public function delete(Product $product): void;
}