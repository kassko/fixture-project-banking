<?php

declare(strict_types=1);

namespace App\DTO\Request;

class PaymentScheduleRequest
{
    public function __construct(
        private int $customerId,
        private float $amount,
        private string $currency,
        private string $frequency,
        private string $startDate,
        private ?string $endDate = null,
        private ?int $occurrences = null,
        private string $type = 'RECURRING',
        private ?string $description = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function getOccurrences(): ?int
    {
        return $this->occurrences;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['amount'] ?? 0.0,
            $data['currency'] ?? 'EUR',
            $data['frequency'] ?? 'MONTHLY',
            $data['startDate'] ?? date('Y-m-d'),
            $data['endDate'] ?? null,
            $data['occurrences'] ?? null,
            $data['type'] ?? 'RECURRING',
            $data['description'] ?? null
        );
    }
}
