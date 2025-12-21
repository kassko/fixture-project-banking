<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Alert configuration - Legacy nested object (NO Doctrine).
 */
class AlertConfig
{
    private string $alertId;
    
    private string $type;
    
    private array $conditions = [];
    
    private array $recipients = [];
    
    private bool $isEnabled = true;
    
    public function __construct(string $alertId, string $type)
    {
        $this->alertId = $alertId;
        $this->type = $type;
    }
    
    public function getAlertId(): string
    {
        return $this->alertId;
    }
    
    public function getType(): string
    {
        return $this->type;
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
    
    public function addCondition(string $field, string $operator, mixed $threshold): static
    {
        $this->conditions[] = [
            'field' => $field,
            'operator' => $operator,
            'threshold' => $threshold,
        ];
        return $this;
    }
    
    public function getRecipients(): array
    {
        return $this->recipients;
    }
    
    public function setRecipients(array $recipients): static
    {
        $this->recipients = $recipients;
        return $this;
    }
    
    public function addRecipient(string $email): static
    {
        if (!in_array($email, $this->recipients, true)) {
            $this->recipients[] = $email;
        }
        return $this;
    }
    
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
    
    public function setIsEnabled(bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }
}
