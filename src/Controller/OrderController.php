<?php

namespace App\Controller;

use App\Service\Interfaces\OrderProcessingServiceInterface;
use App\Service\Interfaces\OrderRetrievalServiceInterface;
use App\Validator\OrderValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;

class OrderController extends AbstractController
{
    public function __construct(
        private OrderProcessingServiceInterface $orderProcessingService,
        private OrderRetrievalServiceInterface $orderRetrievalService,
        private OrderValidator $orderValidator
        )
    {}

    #[Route('/order', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }

        try {
            $this->orderValidator->validateCreateOrder($data);
            $order = $this->orderProcessingService->createOrder($data['items']);
            if ($order == null) {
                return new JsonResponse(['error' => 'Order was not created!'], 404);
            }
            return new JsonResponse([
                'message' => 'Order created',
                'order' => [
                    'id' => $order->getId(),
                    'totalPrice' => $order->getTotalPrice()->getAmount(),
                    'currency' => $order->getCurrency(),
                    'items' => array_map(function ($item) {
                        return [
                            'productId' => $item->getProduct()->getId(),
                            'quantity' => $item->getQuantity(),
                        ];
                    }, $order->getOrderItems()->toArray())
                ]
            ], 200);
        } catch (ValidatorException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/order/{id}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $id): JsonResponse
    {
        try {
            $this->orderValidator->validateGetOrder($id);
            $order = $this->orderRetrievalService->getOrder($id);
            if ($order == null) {
                return new JsonResponse(['error' => 'Order not found'], 404);
            }

            return new JsonResponse([
                'order' => [
                    'id' => $order->getId(),
                    'totalPrice' => $order->getTotalPrice()->getAmount(),
                    'currency' => $order->getCurrency(),
                    'items' => array_map(function ($item) {
                        return [
                            'productId' => $item->getProduct()->getId(),
                            'quantity' => $item->getQuantity(),
                        ];
                    }, $order->getOrderItems()->toArray())
                ]
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
