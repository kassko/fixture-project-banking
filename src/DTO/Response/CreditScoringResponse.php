<?php

declare(strict_types=1);

namespace App\DTO\Response;

class CreditScoringResponse
{
    public function __construct(
        private int $customerId,
        private int $creditScore,
        private string $scoreRating,
        private ?ScoreBreakdown $breakdown = null,
        private ?array $recommendations = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCreditScore(): int
    {
        return $this->creditScore;
    }

    public function getScoreRating(): string
    {
        return $this->scoreRating;
    }

    public function getBreakdown(): ?ScoreBreakdown
    {
        return $this->breakdown;
    }

    public function getRecommendations(): ?array
    {
        return $this->recommendations;
    }

    public function toArray(): array
    {
        $result = [
            'customer_id' => $this->customerId,
            'credit_score' => $this->creditScore,
            'score_rating' => $this->scoreRating,
        ];

        if ($this->breakdown !== null) {
            $result['breakdown'] = $this->breakdown->toArray();
        }

        if ($this->recommendations !== null) {
            $result['recommendations'] = $this->recommendations;
        }

        return $result;
    }
}
