<?php

namespace App\Service\Calculators;

use App\Entity\Order;
use App\Service\Interfaces\CalculatorInterface;

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