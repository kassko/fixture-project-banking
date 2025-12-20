<?php

declare(strict_types=1);

namespace App\Model\Financial;

use DateTimeImmutable;

/**
 * Represents an amortization schedule for a loan.
 */
class AmortizationSchedule
{
    /**
     * @param array<array{
     *     period: int,
     *     paymentDate: DateTimeImmutable,
     *     payment: MoneyAmount,
     *     principal: MoneyAmount,
     *     interest: MoneyAmount,
     *     balance: MoneyAmount
     * }> $schedule
     */
    public function __construct(
        private MoneyAmount $loanAmount,
        private InterestRate $interestRate,
        private int $numberOfPayments,
        private PaymentPlan $paymentPlan,
        private array $schedule,
    ) {
    }
    
    public function getLoanAmount(): MoneyAmount
    {
        return $this->loanAmount;
    }
    
    public function getInterestRate(): InterestRate
    {
        return $this->interestRate;
    }
    
    public function getNumberOfPayments(): int
    {
        return $this->numberOfPayments;
    }
    
    public function getPaymentPlan(): PaymentPlan
    {
        return $this->paymentPlan;
    }
    
    /**
     * @return array<array{
     *     period: int,
     *     paymentDate: DateTimeImmutable,
     *     payment: MoneyAmount,
     *     principal: MoneyAmount,
     *     interest: MoneyAmount,
     *     balance: MoneyAmount
     * }>
     */
    public function getSchedule(): array
    {
        return $this->schedule;
    }
    
    public function getTotalInterest(): MoneyAmount
    {
        $total = 0.0;
        
        foreach ($this->schedule as $entry) {
            $total += $entry['interest']->getAmount();
        }
        
        return new MoneyAmount($total, $this->loanAmount->getCurrency());
    }
    
    public function getPaymentForPeriod(int $period): ?array
    {
        foreach ($this->schedule as $entry) {
            if ($entry['period'] === $period) {
                return $entry;
            }
        }
        
        return null;
    }
}
