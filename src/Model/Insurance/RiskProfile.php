<?php

declare(strict_types=1);

namespace App\Model\Insurance;

use App\Enum\RiskCategory;

/**
 * Represents a risk profile assessment.
 */
class RiskProfile
{
    /**
     * @param array<string, mixed> $factors
     */
    public function __construct(
        private int $score,
        private RiskCategory $category,
        private array $factors,
        private ?\DateTimeImmutable $lastAssessment = null,
    ) {
        if ($score < 0 || $score > 100) {
            throw new \InvalidArgumentException('Risk score must be between 0 and 100');
        }
    }
    
    public function getScore(): int
    {
        return $this->score;
    }
    
    public function getCategory(): RiskCategory
    {
        return $this->category;
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getFactors(): array
    {
        return $this->factors;
    }
    
    public function getLastAssessment(): ?\DateTimeImmutable
    {
        return $this->lastAssessment;
    }
    
    public function getFactor(string $key): mixed
    {
        return $this->factors[$key] ?? null;
    }
    
    public function isHighRisk(): bool
    {
        return $this->category === RiskCategory::HIGH || $this->category === RiskCategory::CRITICAL;
    }
}
