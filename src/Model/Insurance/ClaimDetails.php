<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Model\Common\DocumentReference;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;

/**
 * Represents detailed information about an insurance claim.
 */
class ClaimDetails
{
    /**
     * @param array<DocumentReference> $supportingDocuments
     */
    public function __construct(
        private string $claimNumber,
        private DateTimeImmutable $incidentDate,
        private string $incidentDescription,
        private MoneyAmount $claimedAmount,
        private string $status,
        private array $supportingDocuments = [],
        private ?MoneyAmount $approvedAmount = null,
        private ?DateTimeImmutable $approvalDate = null,
    ) {
    }
    
    public function getClaimNumber(): string
    {
        return $this->claimNumber;
    }
    
    public function getIncidentDate(): DateTimeImmutable
    {
        return $this->incidentDate;
    }
    
    public function getIncidentDescription(): string
    {
        return $this->incidentDescription;
    }
    
    public function getClaimedAmount(): MoneyAmount
    {
        return $this->claimedAmount;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * @return array<DocumentReference>
     */
    public function getSupportingDocuments(): array
    {
        return $this->supportingDocuments;
    }
    
    public function getApprovedAmount(): ?MoneyAmount
    {
        return $this->approvedAmount;
    }
    
    public function getApprovalDate(): ?DateTimeImmutable
    {
        return $this->approvalDate;
    }
    
    public function isApproved(): bool
    {
        return $this->approvedAmount !== null && $this->approvalDate !== null;
    }
    
    public function addDocument(DocumentReference $document): void
    {
        $this->supportingDocuments[] = $document;
    }
}
