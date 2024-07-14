<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\Interface\ProductRepositoryInterface;
use App\Service\Interface\OrderProcessingServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Interface\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\Interface\CalculatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Exception;


class OrderProcessingService implements OrderProcessingServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private OrderRepositoryInterface $orderRepository,
        private LoggerInterface $logger,
        private ValidatorInterface $validator,
        private CalculatorInterface $calculator
    )
    {}

    /**
     * @param array<array{productId: int, quantity: int}> $items
     */
    public function createOrder(array $items): Order
    {
        $order = new Order();

        foreach ($items as $item) {
            $product = $this->productRepository->find($item['productId']);
            if(!$product) {
                $this->logger->error("Product not found!");
                throw new Exception("Product not found!");
            }

            $quantity = $item['quantity'];
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);

            $errors = $this->validator->validate($orderItem);

            if (count($errors) > 0) {
                $errorsString = $this->formatErrors($errors);
                throw new ValidatorException($errorsString);
            }

            $order->addOrderItem($orderItem);
        }

        $this->calculator->calculate($order);

        $errors = $this->validator->validate($order);

        if (count($errors) > 0) {
            $errorsString = $this->formatErrors($errors);
            throw new ValidatorException($errorsString);
        }

        $this->orderRepository->save($order);

        return $order;
    }

    private function formatErrors(ConstraintViolationListInterface $errors): string
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return implode(', ', $errorMessages);
    }
}