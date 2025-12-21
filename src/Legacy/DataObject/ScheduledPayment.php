<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

use DateTimeImmutable;

/**
 * Scheduled payment - NO Doctrine annotations.
 */
class ScheduledPayment
{
    private DateTimeImmutable $dueDate;
    
    private float $amount;
    
    private float $principal;
    
    private float $interest;
    
    private string $status = 'PENDING';
    
    private ?DateTimeImmutable $paidDate = null;
    
    /** @var PaymentPenalty[] */
    private array $penalties = [];
    
    public function __construct(DateTimeImmutable $dueDate, float $amount, float $principal, float $interest)
    {
        $this->dueDate = $dueDate;
        $this->amount = $amount;
        $this->principal = $principal;
        $this->interest = $interest;
    }
    
    public function getDueDate(): DateTimeImmutable
    {
        return $this->dueDate;
    }
    
    public function setDueDate(DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;
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
    
    public function getPrincipal(): float
    {
        return $this->principal;
    }
    
    public function setPrincipal(float $principal): static
    {
        $this->principal = $principal;
        return $this;
    }
    
    public function getInterest(): float
    {
        return $this->interest;
    }
    
    public function setInterest(float $interest): static
    {
        $this->interest = $interest;
        return $this;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
    
    public function getPaidDate(): ?DateTimeImmutable
    {
        return $this->paidDate;
    }
    
    public function setPaidDate(?DateTimeImmutable $paidDate): static
    {
        $this->paidDate = $paidDate;
        return $this;
    }
    
    public function getPenalties(): array
    {
        return $this->penalties;
    }
    
    public function setPenalties(array $penalties): static
    {
        $this->penalties = $penalties;
        return $this;
    }
    
    public function addPenalty(PaymentPenalty $penalty): static
    {
        $this->penalties[] = $penalty;
        return $this;
    }
    
    public function getTotalWithPenalties(): float
    {
        $total = $this->amount;
        foreach ($this->penalties as $penalty) {
            $total += $penalty->getAmount();
        }
        return $total;
    }
}
