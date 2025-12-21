<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use DateTimeImmutable;

/**
 * Workflow - Legacy nested object (NO Doctrine).
 */
class Workflow
{
    private string $id;
    
    private string $name;
    
    private array $steps = [];
    
    private string $currentStep;
    
    private DateTimeImmutable $startedAt;
    
    private ?DateTimeImmutable $completedAt = null;
    
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->currentStep = 'INITIAL';
        $this->startedAt = new DateTimeImmutable();
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getSteps(): array
    {
        return $this->steps;
    }
    
    public function setSteps(array $steps): static
    {
        $this->steps = $steps;
        return $this;
    }
    
    public function addStep(string $stepName, array $stepData): static
    {
        $this->steps[$stepName] = $stepData;
        return $this;
    }
    
    public function getCurrentStep(): string
    {
        return $this->currentStep;
    }
    
    public function setCurrentStep(string $currentStep): static
    {
        $this->currentStep = $currentStep;
        return $this;
    }
    
    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }
    
    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }
    
    public function complete(): static
    {
        $this->completedAt = new DateTimeImmutable();
        return $this;
    }
    
    public function isCompleted(): bool
    {
        return $this->completedAt !== null;
    }
}
