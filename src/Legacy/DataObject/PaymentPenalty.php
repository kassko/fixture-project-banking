<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

use DateTimeImmutable;

/**
 * Payment penalty - NO Doctrine annotations.
 */
class PaymentPenalty
{
    private string $type;
    
    private float $amount;
    
    private DateTimeImmutable $appliedDate;
    
    private string $reason;
    
    public function __construct(string $type, float $amount, DateTimeImmutable $appliedDate, string $reason = '')
    {
        $this->type = $type;
        $this->amount = $amount;
        $this->appliedDate = $appliedDate;
        $this->reason = $reason;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }
    
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }
    
    public function getAppliedDate(): DateTimeImmutable
    {
        return $this->appliedDate;
    }
    
    public function setAppliedDate(DateTimeImmutable $appliedDate): static
    {
        $this->appliedDate = $appliedDate;
        return $this;
    }
    
    public function getReason(): string
    {
        return $this->reason;
    }
    
    public function setReason(string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }
}
