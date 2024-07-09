<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Brick\Money\Money;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderItemTest extends KernelTestCase
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

    public function testCreateOrderItem(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($product);

        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($order);

        $orderItem = new OrderItem();
        $orderItem->setQuantity(2);
        $orderItem->setOrder($order);
        $orderItem->setProduct($product);

        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        $savedOrderItem = $this->entityManager->find(OrderItem::class, $orderItem->getId());

        $this->assertNotNull($savedOrderItem);
        $this->assertSame($orderItem->getId(), $savedOrderItem->getId());
        $this->assertSame($orderItem->getQuantity(), $savedOrderItem->getQuantity());
        $this->assertSame($orderItem->getOrder()->getId(), $savedOrderItem->getOrder()->getId());
        $this->assertSame($orderItem->getProduct()->getId(), $savedOrderItem->getProduct()->getId());
    }
}
