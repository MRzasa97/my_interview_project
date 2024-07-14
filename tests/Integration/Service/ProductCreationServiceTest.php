<?php

namespace App\Tests\Integration\Service;

use App\Entity\Product;
use App\Repository\Interface\ProductRepositoryInterface;
use App\Service\ProductCreationService;
use Brick\Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductCreationServiceTest extends KernelTestCase
{
    private ?ProductCreationService $productCreationService = null;
    private ?ProductRepositoryInterface $productRepository = null;
    private ?ValidatorInterface $validator = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->productRepository = $container->get(ProductRepositoryInterface::class);
        $this->validator = $container->get(ValidatorInterface::class);

        $this->productCreationService = new ProductCreationService(
            $this->productRepository,
            $this->validator
        );
    }

    public function testCreateProductSuccessfully(): void
    {
        $name = "Test Product";
        $price = Money::of(1000, 'USD');

        $product = $this->productCreationService->createProduct($name, $price);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());

        $savedProduct = $this->productRepository->find($product->getId());
        $this->assertNotNull($savedProduct);
        $this->assertEquals($name, $savedProduct->getName());
        $this->assertEquals($price, $savedProduct->getPrice());
    }

    public function testCreateProductWithValidationErrors(): void
    {
        $this->expectException(ValidatorException::class);

        $name = "";
        $price = Money::of(1000, 'USD');

        $this->productCreationService->createProduct($name, $price);
    }

    public function testCreateProductWithNegativePrice(): void
    {
        $this->expectException(ValidatorException::class);

        $name = "Test Product";
        $price = Money::of(-1000, 'USD');

        $this->productCreationService->createProduct($name, $price);
    }

    public function testCreateProductWithZeroPrice(): void
    {
        $name = "Test Product";
        $price = Money::of(0, 'USD');

        $product = $this->productCreationService->createProduct($name, $price);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());

        $savedProduct = $this->productRepository->find($product->getId());
        $this->assertNotNull($savedProduct);
        $this->assertEquals($name, $savedProduct->getName());
        $this->assertEquals($price, $savedProduct->getPrice());
    }
}
