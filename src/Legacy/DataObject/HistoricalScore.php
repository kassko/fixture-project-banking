<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

use DateTimeImmutable;

/**
 * Historical score - NO Doctrine annotations.
 */
class HistoricalScore
{
    private float $score;
    
    private DateTimeImmutable $assessmentDate;
    
    private string $assessorId;
    
    private array $factors = [];
    
    private string $notes = '';
    
    public function __construct(float $score, DateTimeImmutable $assessmentDate, string $assessorId)
    {
        $this->score = $score;
        $this->assessmentDate = $assessmentDate;
        $this->assessorId = $assessorId;
    }
    
    public function getScore(): float
    {
        return $this->score;
    }
    
    public function setScore(float $score): static
    {
        $this->score = $score;
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
    
    public function getAssessorId(): string
    {
        return $this->assessorId;
    }
    
    public function setAssessorId(string $assessorId): static
    {
        $this->assessorId = $assessorId;
        return $this;
    }
    
    public function getFactors(): array
    {
        return $this->factors;
    }
    
    public function setFactors(array $factors): static
    {
        $this->factors = $factors;
        return $this;
    }
    
    public function addFactor(string $key, mixed $value): static
    {
        $this->factors[$key] = $value;
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
}
