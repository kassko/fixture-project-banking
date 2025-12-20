<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

/**
 * Legacy payment schedule data object WITHOUT Doctrine annotations.
 * Represents payment schedule data from a legacy system.
 */
class LegacyPaymentSchedule
{
    private ?string $scheduleId = null;
    
    private ?string $accountNumber = null;
    
    private ?array $payments = null;
    
    private ?float $totalAmount = null;
    
    private ?string $currency = null;
    
    private ?string $frequency = null;
    
    private ?string $startDate = null;
    
    private ?string $endDate = null;
    
    private ?int $numberOfPayments = null;
    
    private ?float $interestRate = null;
    
    private ?string $status = null;
    
    public function getScheduleId(): ?string
    {
        return $this->scheduleId;
    }
    
    public function setScheduleId(?string $scheduleId): self
    {
        $this->scheduleId = $scheduleId;
        return $this;
    }
    
    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }
    
    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }
    
    public function getPayments(): ?array
    {
        return $this->payments;
    }
    
    public function setPayments(?array $payments): self
    {
        $this->payments = $payments;
        return $this;
    }
    
    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }
    
    public function setTotalAmount(?float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }
    
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
    
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function getFrequency(): ?string
    {
        return $this->frequency;
    }
    
    public function setFrequency(?string $frequency): self
    {
        $this->frequency = $frequency;
        return $this;
    }
    
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }
    
    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }
    
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }
    
    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getNumberOfPayments(): ?int
    {
        return $this->numberOfPayments;
    }
    
    public function setNumberOfPayments(?int $numberOfPayments): self
    {
        $this->numberOfPayments = $numberOfPayments;
        return $this;
    }
    
    public function getInterestRate(): ?float
    {
        return $this->interestRate;
    }
    
    public function setInterestRate(?float $interestRate): self
    {
        $this->interestRate = $interestRate;
        return $this;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }
    
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
