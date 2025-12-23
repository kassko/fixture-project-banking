<?php

declare(strict_types=1);

namespace App\DTO\Response;

class FraudDetectionResponse
{
    public function __construct(
        private int $transactionId,
        private int $customerId,
        private float $fraudScore,
        private string $riskLevel,
        private array $detectedPatterns,
        private bool $isBlocked,
        private ?FraudAlert $alert = null,
        private ?array $recommendations = null
    ) {
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getFraudScore(): float
    {
        return $this->fraudScore;
    }

    public function getRiskLevel(): string
    {
        return $this->riskLevel;
    }

    public function getDetectedPatterns(): array
    {
        return $this->detectedPatterns;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function getAlert(): ?FraudAlert
    {
        return $this->alert;
    }

    public function toArray(): array
    {
        $result = [
            'transaction_id' => $this->transactionId,
            'customer_id' => $this->customerId,
            'fraud_score' => $this->fraudScore,
            'risk_level' => $this->riskLevel,
            'detected_patterns' => $this->detectedPatterns,
            'is_blocked' => $this->isBlocked,
        ];

        if ($this->alert !== null) {
            $result['alert'] = $this->alert->toArray();
        }

        if ($this->recommendations !== null) {
            $result['recommendations'] = $this->recommendations;
        }

        return $result;
    }
}
