<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ScheduledPayment
{
    public function __construct(
        private string $paymentDate,
        private float $amount,
        private string $status,
        private int $sequenceNumber,
        private bool $isBusinessDay
    ) {
    }

    public function toArray(): array
    {
        return [
            'payment_date' => $this->paymentDate,
            'amount' => $this->amount,
            'status' => $this->status,
            'sequence_number' => $this->sequenceNumber,
            'is_business_day' => $this->isBusinessDay,
        ];
    }
}
