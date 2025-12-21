<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Pricing rule - Legacy nested object (NO Doctrine).
 */
class PricingRule
{
    private string $ruleId;
    
    private string $name;
    
    private array $conditions = [];
    
    private array $adjustments = [];
    
    private int $priority = 0;
    
    private bool $isActive = true;
    
    public function __construct(string $ruleId, string $name)
    {
        $this->ruleId = $ruleId;
        $this->name = $name;
    }
    
    public function getRuleId(): string
    {
        return $this->ruleId;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getConditions(): array
    {
        return $this->conditions;
    }
    
    public function setConditions(array $conditions): static
    {
        $this->conditions = $conditions;
        return $this;
    }
    
    public function addCondition(string $field, string $operator, mixed $value): static
    {
        $this->conditions[] = [
            'field' => $field,
            'operator' => $operator,
            'value' => $value,
        ];
        return $this;
    }
    
    public function getAdjustments(): array
    {
        return $this->adjustments;
    }
    
    public function setAdjustments(array $adjustments): static
    {
        $this->adjustments = $adjustments;
        return $this;
    }
    
    public function addAdjustment(string $type, float $value): static
    {
        $this->adjustments[] = [
            'type' => $type,
            'value' => $value,
        ];
        return $this;
    }
    
    public function getPriority(): int
    {
        return $this->priority;
    }
    
    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
