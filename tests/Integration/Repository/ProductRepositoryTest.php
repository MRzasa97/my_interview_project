<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Product;
use App\Repository\Interface\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Brick\Money\Money;

class ProductRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?ProductRepositoryInterface $productRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->productRepository = $kernel->getContainer()->get(ProductRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testFindAll(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $products = $this->productRepository->findAll();

        $this->assertCount(1, $products);
        $this->assertEquals('Test Product', $products[0]->getName());
    }

    public function testFindBy(): void
    {
        $product1 = new Product();
        $product1->setName('Product 1');
        $product1->setPrice(Money::ofMinor(2000, 'USD'));

        $product2 = new Product();
        $product2->setName('Product 2');
        $product2->setPrice(Money::ofMinor(3000, 'USD'));

        $this->entityManager->persist($product1);
        $this->entityManager->persist($product2);
        $this->entityManager->flush();

        $criteria = ['price' => 2000];
        $products = $this->productRepository->findBy($criteria);

        $this->assertCount(1, $products);
        $this->assertEquals('Product 1', $products[0]->getName());
    }

    public function testFindOneBy(): void
    {
        $product = new Product();
        $product->setName('Unique Product');
        $product->setPrice(Money::ofMinor(4000, 'USD'));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $criteria = ['name' => 'Unique Product'];
        $foundProduct = $this->productRepository->findOneBy($criteria);

        $this->assertNotNull($foundProduct);
        $this->assertEquals('Unique Product', $foundProduct->getName());
    }

    public function testFind(): void
    {
        $product = new Product();
        $product->setName('Another Product');
        $product->setPrice(Money::ofMinor(5000, 'USD'));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $foundProduct = $this->productRepository->find($product->getId());

        $this->assertNotNull($foundProduct);
        $this->assertEquals('Another Product', $foundProduct->getName());
    }
}
