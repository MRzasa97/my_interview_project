<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Brick\Money\Money;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testCreateOrderWithItems(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($product);

        $orderItem = new OrderItem();
        $orderItem->setQuantity(2);
        $orderItem->setProduct($product);

        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(2000, 'USD'));
        $order->setVatPrice(Money::ofMinor(2000, 'USD'));
        $order->setNetPrice(Money::ofMinor(2000, 'USD'));
        $order->addOrderItem($orderItem);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $savedOrder = $this->entityManager->find(Order::class, $order->getId());

        $this->assertNotNull($savedOrder);
        $this->assertSame($order->getId(), $savedOrder->getId());
        $this->assertEquals($order->getTotalPrice(), $savedOrder->getTotalPrice());
        $this->assertEquals($order->getVatPrice(), $savedOrder->getVatPrice());
        $this->assertCount(1, $savedOrder->getOrderItems());

        $savedOrderItem = $savedOrder->getOrderItems()->first();
        $this->assertNotNull($savedOrderItem);
        $this->assertSame($orderItem->getQuantity(), $savedOrderItem->getQuantity());
        $this->assertSame($product->getId(), $savedOrderItem->getProduct()->getId());
    }
}
