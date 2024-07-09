<?php

namespace App\Service;

use App\Entity\Order;
use App\Service\Interfaces\CalculatorInterface;

class OrderPriceCalculatorService implements CalculatorInterface
{
    /**
     * @var CalculatorInterface[]
     */
    private array $calculators;

    /**
     * @param iterable<CalculatorInterface> $calculators
     */
    public function __construct(
        iterable $calculators
    )
    {
        foreach ($calculators as $calculator) {
            $this->calculators[] = $calculator;
        }

        usort($this->calculators, fn($a, $b) => $b->getPriority() - $a->getPriority()); 
    }

    public function calculate(Order $order): void
    {
        foreach($this->calculators as $calculator) {
            $calculator->calculate($order);
        }
    }

    public function getPriority(): int
    {
        return 0;
    }
}