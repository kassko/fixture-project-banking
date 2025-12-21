<?php

declare(strict_types=1);

namespace App\Model\Financial;

/**
 * Represents an interest rate with calculation methods.
 */
class InterestRate
{
    public function __construct(
        private float $rate,
        private string $type = 'annual', // annual, monthly, daily
        private bool $isCompound = false,
    ) {
        if ($rate < 0 || $rate > 100) {
            throw new \InvalidArgumentException('Interest rate must be between 0 and 100');
        }
    }
    
    public function getRate(): float
    {
        return $this->rate;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function isCompound(): bool
    {
        return $this->isCompound;
    }
    
    public function getDecimalRate(): float
    {
        return $this->rate / 100;
    }
    
    public function calculateInterest(MoneyAmount $principal, int $periods): MoneyAmount
    {
        $rate = $this->getDecimalRate();
        
        if ($this->isCompound) {
            $amount = $principal->getAmount() * pow(1 + $rate, $periods) - $principal->getAmount();
        } else {
            $amount = $principal->getAmount() * $rate * $periods;
        }
        
        return new MoneyAmount($amount, $principal->getCurrency());
    }
    
    public function toMonthlyRate(): self
    {
        if ($this->type === 'monthly') {
            return $this;
        }
        
        if ($this->type === 'annual') {
            return new self($this->rate / 12, 'monthly', $this->isCompound);
        }
        
        throw new \LogicException('Cannot convert daily rate to monthly');
    }
}
