<?php

namespace App\Tests\Integration\Entity;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Brick\Money\Money;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testProductEntity(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setPrice(Money::ofMinor(1000, 'USD'));

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->entityManager->clear();

        $query = $this->entityManager->createQuery(
            'SELECT p FROM App\Entity\Product p WHERE p.name = :name'
        )->setParameter('name', 'Test Product');

        $retrievedProduct = $query->getSingleResult();

        $this->assertInstanceOf(Product::class, $retrievedProduct);
        $this->assertEquals('Test Product', $retrievedProduct->getName());
        $this->assertEquals(1000, $retrievedProduct->getPrice()->getMinorAmount()->toInt());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
