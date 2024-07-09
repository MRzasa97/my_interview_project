<?php

namespace App\Service\Calculators;

use App\Entity\Order;
use App\Service\Interfaces\CalculatorInterface;
use Brick\Money\Money;
use Brick\Math\RoundingMode;

class TotalPriceCalculator implements CalculatorInterface
{
    public function calculate(Order $order): void
    {
        $vatPrice = Money::ofMinor(0, $order->getCurrency());
        foreach($order->getOrderItems() as $orderItem) {
            $productPrice = $orderItem->getProduct()->getPrice();
            $quantity = $orderItem->getQuantity();
            $itemPrice = $productPrice->multipliedBy($quantity, RoundingMode::DOWN);
            $vatPrice = $vatPrice->plus($itemPrice);
        }

        $order->setTotalPrice($vatPrice);
    }

    public function getPriority(): int
    {
        return 100;
    }
}