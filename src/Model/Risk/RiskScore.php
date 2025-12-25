<?php

namespace App\Model\Risk;

use App\Traits\IdentifiableTrait;
use App\Traits\TimestampableTrait;

class RiskScore
{
    use IdentifiableTrait;
    use TimestampableTrait;

    private ?int $customerId = null;
    private ?string $scoreType = null;
    private ?int $value = null;
    private ?string $source = null;

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getScoreType(): ?string
    {
        return $this->scoreType;
    }

    public function setScoreType(?string $scoreType): self
    {
        $this->scoreType = $scoreType;
        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;
        return $this;
    }
}
