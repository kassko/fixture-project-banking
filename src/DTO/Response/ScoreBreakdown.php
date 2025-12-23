<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ScoreBreakdown
{
    public function __construct(
        private array $criteria,
        private array $weights,
        private array $scores,
        private string $calculatedAt
    ) {
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function getWeights(): array
    {
        return $this->weights;
    }

    public function getScores(): array
    {
        return $this->scores;
    }

    public function getCalculatedAt(): string
    {
        return $this->calculatedAt;
    }

    public function toArray(): array
    {
        return [
            'criteria' => $this->criteria,
            'weights' => $this->weights,
            'scores' => $this->scores,
            'calculated_at' => $this->calculatedAt,
        ];
    }
}
