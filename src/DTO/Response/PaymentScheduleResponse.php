<?php

declare(strict_types=1);

namespace App\DTO\Response;

class PaymentScheduleResponse
{
    public function __construct(
        private string $scheduleId,
        private int $customerId,
        private float $amount,
        private string $currency,
        private string $frequency,
        private string $startDate,
        private ?string $endDate,
        private string $status,
        private array $payments,
        private array $summary
    ) {
    }

    public function getScheduleId(): string
    {
        return $this->scheduleId;
    }

    public function getPayments(): array
    {
        return $this->payments;
    }

    public function toArray(): array
    {
        return [
            'schedule_id' => $this->scheduleId,
            'customer_id' => $this->customerId,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'frequency' => $this->frequency,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'status' => $this->status,
            'payments' => array_map(fn(ScheduledPayment $p) => $p->toArray(), $this->payments),
            'summary' => $this->summary,
        ];
    }
}
