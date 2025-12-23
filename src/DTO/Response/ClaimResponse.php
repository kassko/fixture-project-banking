<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ClaimResponse
{
    public function __construct(
        private int $claimId,
        private int $customerId,
        private string $type,
        private string $status,
        private string $description,
        private string $incidentDate,
        private string $createdAt,
        private ?string $resolvedAt,
        private ?float $amount,
        private array $statusHistory,
        private array $slaMetrics
    ) {
    }

    public function getClaimId(): int
    {
        return $this->claimId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIncidentDate(): string
    {
        return $this->incidentDate;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getResolvedAt(): ?string
    {
        return $this->resolvedAt;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getStatusHistory(): array
    {
        return $this->statusHistory;
    }

    public function getSlaMetrics(): array
    {
        return $this->slaMetrics;
    }

    public function toArray(): array
    {
        return [
            'claim_id' => $this->claimId,
            'customer_id' => $this->customerId,
            'type' => $this->type,
            'status' => $this->status,
            'description' => $this->description,
            'incident_date' => $this->incidentDate,
            'created_at' => $this->createdAt,
            'resolved_at' => $this->resolvedAt,
            'amount' => $this->amount,
            'status_history' => $this->statusHistory,
            'sla_metrics' => $this->slaMetrics,
        ];
    }
}
