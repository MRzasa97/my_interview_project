<?php

namespace App\Repository;

use App\Entity\Order;
use App\Repository\Interface\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function find(int $id): ?Order
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.id = :id')
            ->setParameter('id', $id);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return Order[]
     */
    public function findAll(): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return Order[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("o.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy("o.$field", $direction);
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
     * @return Order|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?Order
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("o.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy("o.$field", $direction);
            }
        }

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function delete(Order $order): void
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }
}
