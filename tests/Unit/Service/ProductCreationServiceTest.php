<?php

namespace App\Unit\Tests\Service;

use App\Entity\Product;
use App\Repository\Interface\ProductRepositoryInterface;
use App\Service\ProductCreationService;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;

class ProductCreationServiceTest extends TestCase
{
    private $productRepository;
    private $validator;
    private $productCreationService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->productCreationService = new ProductCreationService(
            $this->productRepository,
            $this->validator
        );
    }

    public function testCreateProductSuccessfully(): void
    {
        $name = "Test Product";
        $price = Money::of(1000, 'USD');

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->productRepository->expects($this->once())
            ->method('save');

        $product = $this->productCreationService->createProduct($name, $price);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
    }

    public function testCreateProductWithValidationErrors(): void
    {
        $name = "";
        $price = Money::of(1000, 'USD');

        $constraintViolation = new ConstraintViolation(
            'This value should not be blank.',
            '',
            [],
            '',
            'name',
            ''
        );

        $constraintViolationList = new ConstraintViolationList([$constraintViolation]);

        $this->validator->method('validate')
            ->willReturn($constraintViolationList);

        $this->productRepository->expects($this->never())
            ->method('save');

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('This value should not be blank.');

        $this->productCreationService->createProduct($name, $price);
    }

    public function testCreateProductWithNegativePrice(): void
    {
        $name = "Test Product";
        $price = Money::of(-1000, 'USD');

        $constraintViolation = new ConstraintViolation(
            'The price must be zero or positive number',
            '',
            [],
            '',
            'price',
            -1000
        );

        $constraintViolationList = new ConstraintViolationList([$constraintViolation]);

        $this->validator->method('validate')
            ->willReturn($constraintViolationList);

        $this->productRepository->expects($this->never())
            ->method('save');

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage('The price must be zero or positive number');

        $this->productCreationService->createProduct($name, $price);
    }

    public function testCreateProductWithZeroPrice(): void
    {
        $name = "Test Product";
        $price = Money::of(0, 'USD');

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->productRepository->expects($this->once())
            ->method('save');

        $product = $this->productCreationService->createProduct($name, $price);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($name, $product->getName());
        $this->assertEquals($price, $product->getPrice());
    }
}
