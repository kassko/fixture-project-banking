<?php

declare(strict_types=1);

namespace App\DTO\Response;

class OnboardingJourneyResponse
{
    public function __construct(
        private string $journeyId,
        private array $steps,
        private array $requiredDocuments,
        private array $welcomeOffers,
        private int $estimatedCompletionTime,
        private ?array $dedicatedAdvisor
    ) {
    }

    public function getJourneyId(): string
    {
        return $this->journeyId;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function getRequiredDocuments(): array
    {
        return $this->requiredDocuments;
    }

    public function getWelcomeOffers(): array
    {
        return $this->welcomeOffers;
    }

    public function getEstimatedCompletionTime(): int
    {
        return $this->estimatedCompletionTime;
    }

    public function getDedicatedAdvisor(): ?array
    {
        return $this->dedicatedAdvisor;
    }
}
