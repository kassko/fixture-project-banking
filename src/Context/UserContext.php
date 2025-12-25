<?php

namespace App\Context;

class UserContext
{
    private ?int $userId = null;
    private string $role = 'user';
    private array $permissions = [];

    public function __construct(?int $userId = null, string $role = 'user', array $permissions = [])
    {
        $this->userId = $userId;
        $this->role = $role;
        $this->permissions = $permissions;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }
}
