<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use DateTimeImmutable;

/**
 * Investment holding - Legacy nested object (NO Doctrine).
 */
class Holding
{
    private string $symbol;
    
    private string $name;
    
    private float $quantity;
    
    private float $purchasePrice;
    
    private float $currentPrice;
    
    private DateTimeImmutable $purchaseDate;
    
    private string $assetType;
    
    public function __construct(
        string $symbol,
        string $name,
        float $quantity,
        float $purchasePrice,
        DateTimeImmutable $purchaseDate
    ) {
        $this->symbol = $symbol;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->purchasePrice = $purchasePrice;
        $this->currentPrice = $purchasePrice;
        $this->purchaseDate = $purchaseDate;
        $this->assetType = 'STOCK';
    }
    
    public function getSymbol(): string
    {
        return $this->symbol;
    }
    
    public function getName(): string
    {
        return $this->name;
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
    
    public function getPurchasePrice(): float
    {
        return $this->purchasePrice;
    }
    
    public function getCurrentPrice(): float
    {
        return $this->currentPrice;
    }
    
    public function setCurrentPrice(float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;
        return $this;
    }
    
    public function getPurchaseDate(): DateTimeImmutable
    {
        return $this->purchaseDate;
    }
    
    public function getAssetType(): string
    {
        return $this->assetType;
    }
    
    public function setAssetType(string $assetType): static
    {
        $this->assetType = $assetType;
        return $this;
    }
    
    public function getTotalValue(): float
    {
        return $this->quantity * $this->currentPrice;
    }
    
    public function getGainLoss(): float
    {
        return ($this->currentPrice - $this->purchasePrice) * $this->quantity;
    }
    
    public function getGainLossPercentage(): float
    {
        // Use strict comparison with epsilon for floating point
        if (abs($this->purchasePrice) < PHP_FLOAT_EPSILON) {
            return 0.0;
        }
        return (($this->currentPrice - $this->purchasePrice) / $this->purchasePrice) * 100;
    }
}
