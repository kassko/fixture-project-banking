<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use DateTimeImmutable;

/**
 * Tax lot - Legacy nested object (NO Doctrine).
 */
class TaxLot
{
    private string $lotId;
    
    private string $symbol;
    
    private float $quantity;
    
    private float $costBasis;
    
    private DateTimeImmutable $acquisitionDate;
    
    private ?DateTimeImmutable $disposalDate = null;
    
    private string $accountingMethod = 'FIFO';
    
    public function __construct(
        string $lotId,
        string $symbol,
        float $quantity,
        float $costBasis,
        DateTimeImmutable $acquisitionDate
    ) {
        $this->lotId = $lotId;
        $this->symbol = $symbol;
        $this->quantity = $quantity;
        $this->costBasis = $costBasis;
        $this->acquisitionDate = $acquisitionDate;
    }
    
    public function getLotId(): string
    {
        return $this->lotId;
    }
    
    public function getSymbol(): string
    {
        return $this->symbol;
    }
    
    public function getQuantity(): float
    {
        return $this->quantity;
    }
    
    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }
    
    public function getCostBasis(): float
    {
        return $this->costBasis;
    }
    
    public function getAcquisitionDate(): DateTimeImmutable
    {
        return $this->acquisitionDate;
    }
    
    public function getDisposalDate(): ?DateTimeImmutable
    {
        return $this->disposalDate;
    }
    
    public function setDisposalDate(?DateTimeImmutable $disposalDate): static
    {
        $this->disposalDate = $disposalDate;
        return $this;
    }
    
    public function getAccountingMethod(): string
    {
        return $this->accountingMethod;
    }
    
    public function setAccountingMethod(string $accountingMethod): static
    {
        $this->accountingMethod = $accountingMethod;
        return $this;
    }
    
    public function isLongTerm(): bool
    {
        if ($this->disposalDate === null) {
            $referenceDate = new DateTimeImmutable();
        } else {
            $referenceDate = $this->disposalDate;
        }
        
        $holdingPeriod = $this->acquisitionDate->diff($referenceDate);
        return $holdingPeriod->days >= 365;
    }
}
