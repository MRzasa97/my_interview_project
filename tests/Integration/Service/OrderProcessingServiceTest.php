<?php

namespace App\Tests\Integration\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\Interfaces\OrderRepositoryInterface;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Repository\ProductRepository;
use App\Service\Interfaces\CalculatorInterface;
use App\Service\OrderProcessingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Psr\Log\LoggerInterface;
use Brick\Money\Money;
use Exception;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderProcessingServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;
    private ?ProductRepository $productRepository;
    private ?OrderRepositoryInterface $orderRepository;
    private ?OrderProcessingService $orderProcessingService;
    private ?LoggerInterface $logger;
    private ?ValidatorInterface $validator;
    private ?CalculatorInterface $calculator;

    private int $product1Id;
    private int $product2Id;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->productRepository = $kernel->getContainer()->get(ProductRepositoryInterface::class);
        $this->orderRepository = $kernel->getContainer()->get(OrderRepositoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->calculator = $this->createMock(CalculatorInterface::class);

        $this->orderProcessingService = new OrderProcessingService(
            $this->productRepository,
            $this->orderRepository,
            $this->logger,
            $this->validator,
            $this->calculator
        );

        $product1 = new Product();
        $product1->setName('Product 1');
        $product1->setPrice(Money::ofMinor(100, 'USD'));

        $product2 = new Product();
        $product2->setName('Product 2');
        $product2->setPrice(Money::ofMinor(200, 'USD'));

        $this->entityManager->persist($product1);
        $this->entityManager->persist($product2);
        $this->entityManager->flush();

        $this->product1Id = $product1->getId();
        $this->product2Id = $product2->getId();
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\OrderItem')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Order')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();

        parent::tearDown();
    }

    public function testCreateOrderWithValidProducts(): void
    {
        $items = [
            ['productId' => $this->product1Id, 'quantity' => 1],
            ['productId' => $this->product2Id, 'quantity' => 2]
        ];

        $this->orderProcessingService->createOrder($items);

        $orderRepository = $this->orderRepository;
        $orders = $orderRepository->findAll();

        $this->assertCount(1, $orders);

        $order = $orders[0];
        $this->assertCount(2, $order->getOrderItems());

        $orderItems = $order->getOrderItems();
        $this->assertEquals($this->product1Id, $orderItems[0]->getProduct()->getId());
        $this->assertEquals(1, $orderItems[0]->getQuantity());

        $this->assertEquals($this->product2Id, $orderItems[1]->getProduct()->getId());
        $this->assertEquals(2, $orderItems[1]->getQuantity());
    }

    public function testCreateOrderWithInvalidProduct(): void
    {
        $this->logger->expects($this->once())
            ->method('error')
            ->with('Product not found!');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found!');

        $items = [
            ['productId' => 999, 'quantity' => 1]
        ];

        $this->orderProcessingService->createOrder($items);

        $orderRepository = $this->entityManager->getRepository(Order::class);
        $orders = $orderRepository->findAll();

        $this->assertCount(1, $orders);

        $order = $orders[0];
        $this->assertCount(0, $order->getOrderItems());
    }
}
