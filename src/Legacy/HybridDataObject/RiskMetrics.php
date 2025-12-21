<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Risk metrics - Legacy nested object (NO Doctrine).
 */
class RiskMetrics
{
    private float $volatility = 0.0;
    
    private float $sharpeRatio = 0.0;
    
    private float $beta = 1.0;
    
    private float $maxDrawdown = 0.0;
    
    private array $varMetrics = [];
    
    public function getVolatility(): float
    {
        return $this->volatility;
    }
    
    public function setVolatility(float $volatility): static
    {
        $this->volatility = $volatility;
        return $this;
    }
    
    public function getSharpeRatio(): float
    {
        return $this->sharpeRatio;
    }
    
    public function setSharpeRatio(float $sharpeRatio): static
    {
        $this->sharpeRatio = $sharpeRatio;
        return $this;
    }
    
    public function getBeta(): float
    {
        return $this->beta;
    }
    
    public function setBeta(float $beta): static
    {
        $this->beta = $beta;
        return $this;
    }
    
    public function getMaxDrawdown(): float
    {
        return $this->maxDrawdown;
    }
    
    public function setMaxDrawdown(float $maxDrawdown): static
    {
        $this->maxDrawdown = $maxDrawdown;
        return $this;
    }
    
    public function getVarMetrics(): array
    {
        return $this->varMetrics;
    }
    
    public function setVarMetrics(array $varMetrics): static
    {
        $this->varMetrics = $varMetrics;
        return $this;
    }
    
    public function addVarMetric(string $period, float $value): static
    {
        $this->varMetrics[$period] = $value;
        return $this;
    }
}
