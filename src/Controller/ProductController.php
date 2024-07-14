<?php

namespace App\Controller;

use App\Service\Interface\ProductCreationServiceInterface;
use App\Validator\ProductValidator;
use Brick\Money\Money;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{

    public function __construct(
        private ProductCreationServiceInterface $productCreationService,
        private ProductValidator $validator
        )
    {
    }

    #[Route('/product', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }

        try {
            $this->validator->validateCreateProduct($data);
            $name = (string) $data['name'];
            $price = Money::of((string) $data['price'], 'USD');

            $product = $this->productCreationService->createProduct($name, $price);

            return new JsonResponse([
                'status' => 'Product created',
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice() . ' ' . $product->getCurrency()
                ]
            ], 201);
        } catch (ValidatorException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
