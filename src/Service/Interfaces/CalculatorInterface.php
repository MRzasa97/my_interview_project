<?php

namespace App\Service\Interfaces;

use App\Entity\Order;

interface CalculatorInterface
{
    public function calculate(Order $order): void;

    public function getPriority(): int;
}