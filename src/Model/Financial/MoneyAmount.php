<?php

declare(strict_types=1);

namespace App\Model\Financial;

/**
 * Immutable value object representing a monetary amount.
 */
class MoneyAmount
{
    public function __construct(
        private float $amount,
        private string $currency = 'EUR',
    ) {
    }
    
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function add(MoneyAmount $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add amounts in different currencies');
        }
        
        return new self($this->amount + $other->amount, $this->currency);
    }
    
    public function subtract(MoneyAmount $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot subtract amounts in different currencies');
        }
        
        return new self($this->amount - $other->amount, $this->currency);
    }
    
    public function multiply(float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }
    
    public function isZero(): bool
    {
        return abs($this->amount) < 0.01;
    }
    
    public function isPositive(): bool
    {
        return $this->amount > 0;
    }
    
    public function isNegative(): bool
    {
        return $this->amount < 0;
    }
    
    public function format(): string
    {
        return number_format($this->amount, 2, '.', ',') . ' ' . $this->currency;
    }
}
