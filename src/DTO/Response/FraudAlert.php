<?php

declare(strict_types=1);

namespace App\DTO\Response;

class FraudAlert
{
    public function __construct(
        private int $alertId,
        private int $transactionId,
        private int $customerId,
        private string $severity,
        private string $reason,
        private float $fraudScore,
        private array $detectedPatterns,
        private string $status,
        private string $createdAt,
        private ?string $resolvedAt = null
    ) {
    }

    public function getAlertId(): int
    {
        return $this->alertId;
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'alert_id' => $this->alertId,
            'transaction_id' => $this->transactionId,
            'customer_id' => $this->customerId,
            'severity' => $this->severity,
            'reason' => $this->reason,
            'fraud_score' => $this->fraudScore,
            'detected_patterns' => $this->detectedPatterns,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'resolved_at' => $this->resolvedAt,
        ];
    }
}
