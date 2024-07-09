<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\Interfaces\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Brick\Money\Money;

class OrderRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?OrderRepositoryInterface $orderRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->orderRepository = $kernel->getContainer()->get(OrderRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testFindAll(): void
    {
        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $orders = $this->orderRepository->findAll();

        $this->assertCount(1, $orders);
        $this->assertEquals(Money::ofMinor(1000, 'USD'), $orders[0]->getTotalPrice());
    }

    public function testFindBy(): void
    {
        $order1 = new Order();
        $order1->setTotalPrice(Money::ofMinor(2000, 'USD'));
        $order1->setVatPrice(Money::ofMinor(1000, 'USD'));

        $order2 = new Order();
        $order2->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order2->setVatPrice(Money::ofMinor(1000, 'USD'));


        $this->entityManager->persist($order1);
        $this->entityManager->persist($order2);
        $this->entityManager->flush();

        $criteria = ['totalPrice' => 2000];
        $orders = $this->orderRepository->findBy($criteria);

        $this->assertCount(1, $orders);
        $this->assertEquals(Money::ofMinor(2000, 'USD'), $orders[0]->getTotalPrice());
    }

    public function testFindOneBy(): void
    {
        $order = new Order();
        $order->setTotalPrice(Money::ofMinor(1000, 'USD'));
        $order->setVatPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $criteria = ['totalPrice' => 1000];
        $foundOrder = $this->orderRepository->findOneBy($criteria);

        $this->assertNotNull($foundOrder);
        $this->assertEquals(Money::ofMinor(1000, 'USD'), $foundOrder->getTotalPrice());
    }
}
