<?php

namespace App\Context;

class FeatureFlagContext
{
    private array $flags = [];

    public function __construct(array $flags = [])
    {
        $this->flags = $flags;
    }

    public function isEnabled(string $flagName): bool
    {
        return $this->flags[$flagName] ?? false;
    }

    public function setFlag(string $flagName, bool $enabled): self
    {
        $this->flags[$flagName] = $enabled;
        return $this;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function setFlags(array $flags): self
    {
        $this->flags = $flags;
        return $this;
    }
}
