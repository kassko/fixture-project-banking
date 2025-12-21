<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

/**
 * Risk matrix - Legacy nested object (NO Doctrine).
 */
class RiskMatrix
{
    private array $dimensions = [];
    
    private array $scores = [];
    
    private float $overallScore = 0.0;
    
    private string $category = 'MEDIUM';
    
    public function __construct(array $dimensions = [])
    {
        $this->dimensions = $dimensions;
    }
    
    public function getDimensions(): array
    {
        return $this->dimensions;
    }
    
    public function setDimensions(array $dimensions): static
    {
        $this->dimensions = $dimensions;
        return $this;
    }
    
    public function addDimension(string $name, float $weight): static
    {
        $this->dimensions[$name] = $weight;
        return $this;
    }
    
    public function getScores(): array
    {
        return $this->scores;
    }
    
    public function setScores(array $scores): static
    {
        $this->scores = $scores;
        return $this;
    }
    
    public function addScore(string $dimension, float $score): static
    {
        $this->scores[$dimension] = $score;
        return $this;
    }
    
    public function getOverallScore(): float
    {
        return $this->overallScore;
    }
    
    public function setOverallScore(float $overallScore): static
    {
        $this->overallScore = $overallScore;
        return $this;
    }
    
    public function getCategory(): string
    {
        return $this->category;
    }
    
    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }
    
    public function calculateOverallScore(): float
    {
        if (empty($this->scores) || empty($this->dimensions)) {
            return 0.0;
        }
        
        $weightedSum = 0.0;
        $totalWeight = 0.0;
        
        foreach ($this->scores as $dimension => $score) {
            if (isset($this->dimensions[$dimension])) {
                $weight = $this->dimensions[$dimension];
                $weightedSum += $score * $weight;
                $totalWeight += $weight;
            }
        }
        
        $this->overallScore = $totalWeight > 0 ? $weightedSum / $totalWeight : 0.0;
        return $this->overallScore;
    }
}
