<?php

namespace App\Tests\Service\Calculators;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\Calculators\TotalPriceCalculator;
use Brick\Money\Money;
use Brick\Money\Exception\MoneyMismatchException;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TotalPriceCalculatorTest extends TestCase
{
    private $order;
    private $totalPriceCalculator;

    protected function setUp(): void
    {
        $this->order = $this->createMock(Order::class);
        $this->totalPriceCalculator = new TotalPriceCalculator();
    }

    #[DataProvider('totalPriceProvider')]
    public function testCalculateTotalPrice(array $orderItems, string $currency, Money $expectedTotalPrice): void
    {
        $this->order->method('getCurrency')
            ->willReturn($currency);
        $this->order->method('getOrderItems')
            ->willReturn(new ArrayCollection($orderItems));

        $this->order->expects($this->once())
            ->method('setTotalPrice')
            ->with($this->callback(function ($totalPrice) use ($expectedTotalPrice) {
                return $totalPrice->isEqualTo($expectedTotalPrice);
            }));

        $this->totalPriceCalculator->calculate($this->order);

        $this->assertTrue(true);
    }

    public static function totalPriceProvider(): array
    {
        return [
            'multiple items' => [
                self::getOrderItems([
                    ['price' => Money::of(100, 'USD'), 'quantity' => 2],
                    ['price' => Money::of(50, 'USD'), 'quantity' => 3]
                ]),
                'USD',
                Money::of(100 * 2 + 50 * 3, 'USD')
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
                Money::of(100, 'USD')
            ],
        ];
    }

    public function testCalculateTotalPriceWithDifferentCurrencies(): void
    {
        $this->expectException(MoneyMismatchException::class);

        $orderItem1 = $this->createOrderItem(Money::of(100, 'USD'), 2);
        $orderItem2 = $this->createOrderItem(Money::of(50, 'EUR'), 3);
        $orderItems = new ArrayCollection([$orderItem1, $orderItem2]);

        $this->order->method('getCurrency')
            ->willReturn('USD');
        $this->order->method('getOrderItems')
            ->willReturn($orderItems);

        $this->totalPriceCalculator->calculate($this->order);
    }

    public function testGetPriority(): void
    {
        $this->assertEquals(100, $this->totalPriceCalculator->getPriority());
    }

    private function createOrderItem(Money $price, int $quantity): OrderItem
    {
        $product = $this->createMock(Product::class);
        $product->method('getPrice')
            ->willReturn($price);

        $orderItem = $this->createMock(OrderItem::class);
        $orderItem->method('getProduct')
            ->willReturn($product);
        $orderItem->method('getQuantity')
            ->willReturn($quantity);

        return $orderItem;
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
