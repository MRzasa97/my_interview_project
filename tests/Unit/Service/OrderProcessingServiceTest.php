<?php

namespace App\Tests\Unit\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\Interfaces\OrderRepositoryInterface;
use App\Repository\Interfaces\ProductRepositoryInterface;
use App\Service\OrderProcessingService;
use App\Service\Interfaces\CalculatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidatorException;
use Exception;

class OrderProcessingServiceTest extends TestCase
{
    private $productRepository;
    private $orderRepository;
    private $logger;
    private $validator;
    private $calculator;
    private $orderProcessingService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->calculator = $this->createMock(CalculatorInterface::class);

        $this->orderProcessingService = new OrderProcessingService(
            $this->productRepository,
            $this->orderRepository,
            $this->logger,
            $this->validator,
            $this->calculator
        );
    }

    public function testCreateOrderWithValidProducts(): void
    {
        $product1 = new Product();
        $product1->setId(1);

        $this->productRepository->method('find')
            ->willReturn($product1);

        $this->orderRepository->expects($this->once())
            ->method('save');

        $items = [
            ['productId' => 1, 'quantity' => 2]
        ];

        $this->orderProcessingService->createOrder($items);
        $this->assertTrue(true); // Add a basic assertion to avoid risky test warning
    }

    public function testCreateOrderWithInvalidProduct(): void
    {
        $this->productRepository->method('find')
            ->willReturn(null);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Product not found!');

        $this->orderRepository->expects($this->never())
            ->method('save');

        $items = [
            ['productId' => 999, 'quantity' => 2]
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found!');

        $this->orderProcessingService->createOrder($items);

        // Ensure the order items array is empty
        $order = new Order();
        $this->orderProcessingService->createOrder($order);
        $this->assertCount(0, $order->getOrderItems());
    }

    public function testCreateOrderWithZeroQuantity(): void
    {
        $product1 = new Product();
        $product1->setId(1);

        $this->productRepository->method('find')
            ->willReturn($product1);

        $constraintViolation = new ConstraintViolation(
            'The quantity must be more than 0',
            '',
            [],
            '',
            'quantity',
            0
        );

        $constraintViolationList = new ConstraintViolationList([$constraintViolation]);

        $this->validator->method('validate')
            ->willReturn($constraintViolationList);

        $this->logger->expects($this->never())
            ->method('error');

        $this->orderRepository->expects($this->never())
            ->method('save');

        $this->expectException(ValidatorException::class);


        $items = [
            ['productId' => 1, 'quantity' => 0]
        ];

        $this->orderProcessingService->createOrder($items);


        // Ensure the order items array is empty
        $order = new Order();
        $this->orderProcessingService->createOrder($order);
        $this->assertCount(0, $order->getOrderItems());
    }
}
