<?php

namespace App\Service;

use Brick\Money\Money;
use App\Entity\Product;
use App\Service\Interface\ProductCreationServiceInterface;
use App\Repository\Interface\ProductRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\ConstraintViolationListInterface;


class ProductCreationService implements ProductCreationServiceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private ValidatorInterface $validator,
    )
    {}

    public function createProduct(string $name, Money $price): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            $errorsString = $this->formatErrors($errors);
            throw new ValidatorException($errorsString);
        }

        $this->productRepository->save($product);

        return $product;
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