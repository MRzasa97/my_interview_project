<?php

namespace App\Service\Calculator;

use App\Entity\Order;
use App\Service\Interface\CalculatorInterface;
use Brick\Money\Money;
use Brick\Math\RoundingMode;


class VatPriceCalculator implements CalculatorInterface
{
    private const VAT_RATE = 0.23;
    public function calculate(Order $order): void
    {
        $vatPrice = Money::ofMinor(0, $order->getCurrency());
        foreach($order->getOrderItems() as $orderItem) {
            $productPrice = $orderItem->getProduct()->getPrice();
            $quantity = $orderItem->getQuantity();
            $itemPrice = $productPrice->multipliedBy($quantity, RoundingMode::DOWN);
            $itemVatPrice = $itemPrice->multipliedBy(self::VAT_RATE, RoundingMode::DOWN);
            $vatPrice = $vatPrice->plus($itemVatPrice);
        }

        $order->setVatPrice($vatPrice);
    }

    public function getPriority(): int
    {
        return 90;
    }
}