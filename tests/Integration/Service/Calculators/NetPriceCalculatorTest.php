<?php

namespace App\Tests\Service\Calculators;

use App\Entity\Order;
use App\Service\Calculators\NetPriceCalculator;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;

class NetPriceCalculatorTest extends TestCase
{
    private $order;
    private $netPriceCalculator;

    protected function setUp(): void
    {
        $this->order = $this->createMock(Order::class);
        $this->netPriceCalculator = new NetPriceCalculator();
    }

    public function testCalculateNetPriceSuccessfully(): void
    {
        $totalPrice = Money::of(10000, 'USD');
        $vatPrice = Money::of(2300, 'USD');
        $netPrice = Money::of(7700, 'USD');

        $this->order->method('getTotalPrice')
            ->willReturn($totalPrice);
        $this->order->method('getVatPrice')
            ->willReturn($vatPrice);

        $this->order->expects($this->once())
            ->method('setNetPrice')
            ->with($netPrice);

        $this->netPriceCalculator->calculate($this->order);
    }

    public function testCalculateNetPriceWithZeroTotalPrice(): void
    {
        $totalPrice = Money::of(0, 'USD');
        $vatPrice = Money::of(0, 'USD');
        $netPrice = Money::of(0, 'USD');

        $this->order->method('getTotalPrice')
            ->willReturn($totalPrice);
        $this->order->method('getVatPrice')
            ->willReturn($vatPrice);

        $this->order->expects($this->once())
            ->method('setNetPrice')
            ->with($netPrice);

        $this->netPriceCalculator->calculate($this->order);
    }

    public function testCalculateNetPriceWithNegativeTotalPrice(): void
    {
        $totalPrice = Money::of(-10000, 'USD');
        $vatPrice = Money::of(-2300, 'USD');
        $netPrice = Money::of(-7700, 'USD');

        $this->order->method('getTotalPrice')
            ->willReturn($totalPrice);
        $this->order->method('getVatPrice')
            ->willReturn($vatPrice);

        $this->order->expects($this->once())
            ->method('setNetPrice')
            ->with($netPrice);

        $this->netPriceCalculator->calculate($this->order);
    }

    public function testCalculateNetPriceWithZeroVatPrice(): void
    {
        $totalPrice = Money::of(10000, 'USD');
        $vatPrice = Money::of(0, 'USD');
        $netPrice = Money::of(10000, 'USD');

        $this->order->method('getTotalPrice')
            ->willReturn($totalPrice);
        $this->order->method('getVatPrice')
            ->willReturn($vatPrice);

        $this->order->expects($this->once())
            ->method('setNetPrice')
            ->with($netPrice);

        $this->netPriceCalculator->calculate($this->order);
    }

    public function testGetPriority(): void
    {
        $this->assertEquals(80, $this->netPriceCalculator->getPriority());
    }
}
