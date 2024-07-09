<?php

namespace App\Repository;

use App\Entity\OrderItem;
use App\Repository\Interfaces\OrderItemRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function find(int $id): ?OrderItem
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderItem::class, 'o')
            ->where('o.id = :id')
            ->setParameter('id', $id);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return OrderItem[]
     */
    public function findAll(): array
    {
        return $this->entityManager->createQuery('SELECT o FROM App\Entity\OrderItem o')
            ->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return OrderItem[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderItem::class, 'o');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("o.$field = :$field")
                ->setParameter($field, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $queryBuilder->addOrderBy("o.$field", $direction);
            }
        }

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($offset) {
            $queryBuilder->setFirstResult($offset);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @return OrderItem|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?OrderItem
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('o')
            ->from(OrderItem::class, 'o');

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

    public function save(OrderItem $orderItem): void
    {
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();
    }

    public function delete(OrderItem $orderItem): void
    {
        $this->entityManager->remove($orderItem);
        $this->entityManager->flush();
    }
}
