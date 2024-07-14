<?php

namespace App\Service\Interface;

use App\Entity\Order;

interface CalculatorInterface
{
    public function calculate(Order $order): void;

    public function getPriority(): int;
}