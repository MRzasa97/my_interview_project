<?php

namespace App\Tests\Service\Calculators;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\Calculators\VatPriceCalculator;
use Brick\Money\Money;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;


class VatPriceCalculatorTest extends TestCase
{
    private $order;
    private $vatPriceCalculator;

    protected function setUp(): void
    {
        $this->order = $this->createMock(Order::class);
        $this->vatPriceCalculator = new VatPriceCalculator();
    }

    #[DataProvider('vatPriceProvider')]
    public function testCalculateVatPrice(array $orderItems, string $currency, Money $expectedVatPrice): void
    {
        $this->order->method('getCurrency')
            ->willReturn($currency);
        $this->order->method('getOrderItems')
            ->willReturn(new ArrayCollection($orderItems));

        $this->order->expects($this->once())
            ->method('setVatPrice')
            ->with($this->callback(function ($vatPrice) use ($expectedVatPrice) {
                return $vatPrice->isEqualTo($expectedVatPrice);
            }));

        $this->vatPriceCalculator->calculate($this->order);

        $this->assertTrue(true);
    }

    public static function vatPriceProvider(): array
    {
        return [
            'multiple items' => [
                self::getOrderItems([
                    ['price' => Money::of(100, 'USD'), 'quantity' => 2],
                    ['price' => Money::of(50, 'USD'), 'quantity' => 3]
                ]),
                'USD',
                Money::of(100 * 2 * 0.23 + 50 * 3 * 0.23, 'USD')
            ],
            'no items' => [
                [],
                'USD',
                Money::of(0, 'USD')
            ],
            'zero price items' => [
                self::getOrderItems([
                    ['price' => Money::of(0, 'USD'), 'quantity' => 2],
                    ['price' => Money::of(0, 'USD'), 'quantity' => 3]
                ]),
                'USD',
                Money::of(0, 'USD')
            ],
            'single item' => [
                self::getOrderItems([
                    ['price' => Money::of(100, 'USD'), 'quantity' => 1]
                ]),
                'USD',
                Money::of(100 * 0.23, 'USD')
            ],
        ];
    }

    public function testGetPriority(): void
    {
        $this->assertEquals(90, $this->vatPriceCalculator->getPriority());
    }

    private static function getOrderItems(array $items): array
    {
        $orderItems = [];
        foreach ($items as $item) {
            $product = new Product();
            $product->setPrice($item['price']);

            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($item['quantity']);

            $orderItems[] = $orderItem;
        }
        return $orderItems;
    }
}
