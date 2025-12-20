<?php

declare(strict_types=1);

namespace App\Abstract;

use App\Traits\AuditableTrait;
use App\Traits\TimestampableTrait;
use DateTimeImmutable;

/**
 * Base abstract class for all contract entities.
 */
abstract class AbstractContract
{
    use TimestampableTrait;
    use AuditableTrait;
    
    protected string $contractNumber;
    
    protected DateTimeImmutable $startDate;
    
    protected ?DateTimeImmutable $endDate = null;
    
    protected string $status = 'draft';
    
    protected ?string $signedBy = null;
    
    protected ?DateTimeImmutable $signedAt = null;
    
    public function getContractNumber(): string
    {
        return $this->contractNumber;
    }
    
    public function setContractNumber(string $contractNumber): static
    {
        $this->contractNumber = $contractNumber;
        return $this;
    }
    
    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }
    
    public function setStartDate(DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }
    
    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }
    
    public function setEndDate(?DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
    
    public function getSignedBy(): ?string
    {
        return $this->signedBy;
    }
    
    public function setSignedBy(?string $signedBy): static
    {
        $this->signedBy = $signedBy;
        return $this;
    }
    
    public function getSignedAt(): ?DateTimeImmutable
    {
        return $this->signedAt;
    }
    
    public function setSignedAt(?DateTimeImmutable $signedAt): static
    {
        $this->signedAt = $signedAt;
        return $this;
    }
    
    public function isActive(): bool
    {
        $now = new DateTimeImmutable();
        
        return $this->status === 'active'
            && $this->startDate <= $now
            && ($this->endDate === null || $this->endDate >= $now);
    }
    
    public function sign(string $signatory): static
    {
        $this->signedBy = $signatory;
        $this->signedAt = new DateTimeImmutable();
        $this->status = 'signed';
        return $this;
    }
}
