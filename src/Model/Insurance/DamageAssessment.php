<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Model\Common\DocumentReference;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;

/**
 * Damage assessment for insurance claims.
 */
class DamageAssessment
{
    private string $assessorId;
    
    private DateTimeImmutable $assessmentDate;
    
    private MoneyAmount $estimatedCost;
    
    private ?MoneyAmount $approvedAmount = null;
    
    private array $categories = [];
    
    private string $notes = '';
    
    /** @var DocumentReference[] */
    private array $attachments = [];
    
    public function __construct(
        string $assessorId,
        DateTimeImmutable $assessmentDate,
        MoneyAmount $estimatedCost
    ) {
        $this->assessorId = $assessorId;
        $this->assessmentDate = $assessmentDate;
        $this->estimatedCost = $estimatedCost;
    }
    
    public function getAssessorId(): string
    {
        return $this->assessorId;
    }
    
    public function setAssessorId(string $assessorId): static
    {
        $this->assessorId = $assessorId;
        return $this;
    }
    
    public function getAssessmentDate(): DateTimeImmutable
    {
        return $this->assessmentDate;
    }
    
    public function setAssessmentDate(DateTimeImmutable $assessmentDate): static
    {
        $this->assessmentDate = $assessmentDate;
        return $this;
    }
    
    public function getEstimatedCost(): MoneyAmount
    {
        return $this->estimatedCost;
    }
    
    public function setEstimatedCost(MoneyAmount $estimatedCost): static
    {
        $this->estimatedCost = $estimatedCost;
        return $this;
    }
    
    public function getApprovedAmount(): ?MoneyAmount
    {
        return $this->approvedAmount;
    }
    
    public function setApprovedAmount(?MoneyAmount $approvedAmount): static
    {
        $this->approvedAmount = $approvedAmount;
        return $this;
    }
    
    public function getCategories(): array
    {
        return $this->categories;
    }
    
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;
        return $this;
    }
    
    public function addCategory(string $category): static
    {
        if (!in_array($category, $this->categories, true)) {
            $this->categories[] = $category;
        }
        return $this;
    }
    
    public function getNotes(): string
    {
        return $this->notes;
    }
    
    public function setNotes(string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }
    
    public function getAttachments(): array
    {
        return $this->attachments;
    }
    
    public function setAttachments(array $attachments): static
    {
        $this->attachments = $attachments;
        return $this;
    }
    
    public function addAttachment(DocumentReference $attachment): static
    {
        $this->attachments[] = $attachment;
        return $this;
    }
}
