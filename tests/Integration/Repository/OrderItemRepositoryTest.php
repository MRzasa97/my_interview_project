<?php

namespace App\Tests\Integration\Repository;

use App\Entity\OrderItem;
use App\Entity\Product;
use App\Entity\Order;
use App\Repository\Interface\OrderItemRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Brick\Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderItemRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?OrderItemRepositoryInterface $orderItemRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->orderItemRepository = $kernel->getContainer()->get(OrderItemRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testFindAll(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity(1);
        $orderItem->setOrder($order);

        $this->entityManager->persist($product);
        $this->entityManager->persist($order);
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        $orderItems = $this->orderItemRepository->findAll();

        $this->assertCount(1, $orderItems);
        $this->assertEquals('Test Product', $orderItems[0]->getProduct()->getName());
    }

    public function testFindBy(): void
    {
        $product = new Product();
        $product->setName('Another Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity(2);
        $orderItem->setOrder($order);

        $this->entityManager->persist($product);
        $this->entityManager->persist($order);
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        $criteria = ['quantity' => 2];
        $orderItems = $this->orderItemRepository->findBy($criteria);

        $this->assertCount(1, $orderItems);
        $this->assertEquals('Another Test Product', $orderItems[0]->getProduct()->getName());
    }

    public function testFindOneBy(): void
    {
        $product = new Product();
        $product->setName('Unique Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $orderItem = new OrderItem();
        $orderItem->setProduct($product);
        $orderItem->setQuantity(3);
        $orderItem->setOrder($order);

        $this->entityManager->persist($product);
        $this->entityManager->persist($order);
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        $criteria = ['quantity' => 3];
        $foundOrderItem = $this->orderItemRepository->findOneBy($criteria);

        $this->assertNotNull($foundOrderItem);
        $this->assertEquals('Unique Test Product', $foundOrderItem->getProduct()->getName());
    }
}
