<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use DateTimeImmutable;

/**
 * Audit entry - Legacy nested object (NO Doctrine).
 */
class AuditEntry
{
    private DateTimeImmutable $timestamp;
    
    private string $userId;
    
    private string $action;
    
    private array $changes = [];
    
    private string $ipAddress;
    
    public function __construct(string $userId, string $action, string $ipAddress = '')
    {
        $this->timestamp = new DateTimeImmutable();
        $this->userId = $userId;
        $this->action = $action;
        $this->ipAddress = $ipAddress;
    }
    
    public function getTimestamp(): DateTimeImmutable
    {
        return $this->timestamp;
    }
    
    public function getUserId(): string
    {
        return $this->userId;
    }
    
    public function getAction(): string
    {
        return $this->action;
    }
    
    public function getChanges(): array
    {
        return $this->changes;
    }
    
    public function setChanges(array $changes): static
    {
        $this->changes = $changes;
        return $this;
    }
    
    public function addChange(string $field, mixed $oldValue, mixed $newValue): static
    {
        $this->changes[] = [
            'field' => $field,
            'old' => $oldValue,
            'new' => $newValue,
        ];
        return $this;
    }
    
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }
}
