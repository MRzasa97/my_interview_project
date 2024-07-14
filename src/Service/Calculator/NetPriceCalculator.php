<?php

namespace App\Service\Calculator;

use App\Entity\Order;
use App\Service\Interface\CalculatorInterface;

class NetPriceCalculator implements CalculatorInterface
{
    public function calculate(Order $order): void
    {
        $totalPrice = $order->getTotalPrice();

        $vatPrice = $order->getVatPrice();

        $netPrice = $totalPrice->minus($vatPrice);

        $order->setNetPrice($netPrice);
    }

    public function getPriority(): int
    {
        return 80;
    }
}