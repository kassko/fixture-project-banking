<?php

declare(strict_types=1);

namespace App\Model\Financial;

use DateTimeImmutable;

/**
 * Represents a payment plan with scheduled payments.
 */
class PaymentPlan
{
    /**
     * @param array<array{dueDate: DateTimeImmutable, amount: MoneyAmount, description: string}> $payments
     */
    public function __construct(
        private string $planId,
        private MoneyAmount $totalAmount,
        private array $payments,
        private InterestRate $interestRate,
    ) {
    }
    
    public function getPlanId(): string
    {
        return $this->planId;
    }
    
    public function getTotalAmount(): MoneyAmount
    {
        return $this->totalAmount;
    }
    
    /**
     * @return array<array{dueDate: DateTimeImmutable, amount: MoneyAmount, description: string}>
     */
    public function getPayments(): array
    {
        return $this->payments;
    }
    
    public function getInterestRate(): InterestRate
    {
        return $this->interestRate;
    }
    
    public function getNumberOfPayments(): int
    {
        return count($this->payments);
    }
    
    public function getNextPayment(): ?array
    {
        $now = new DateTimeImmutable();
        
        foreach ($this->payments as $payment) {
            if ($payment['dueDate'] > $now) {
                return $payment;
            }
        }
        
        return null;
    }
}
