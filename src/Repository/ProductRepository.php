<?php

namespace App\Repository;

use App\Entity\Product;
use App\Repository\Interface\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function find(int $id): ?Product
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return Product[]
     */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Product[]
     */
    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("p.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy("p.$field", $direction);
            }
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return Product|null
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?Product
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("p.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy("p.$field", $direction);
            }
        }

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }
}
