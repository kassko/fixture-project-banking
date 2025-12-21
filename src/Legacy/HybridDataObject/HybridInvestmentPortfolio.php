<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hybrid investment portfolio with SOME properties managed by Doctrine, others manually.
 */
#[ORM\Entity]
#[ORM\Table(name: 'hybrid_investment_portfolios')]
class HybridInvestmentPortfolio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $portfolioNumber;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $totalValue;
    
    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';
    
    #[ORM\Column(length: 50)]
    private string $riskProfile = 'moderate';
    
    // NOT managed by Doctrine - real-time holdings from external API
    private array $liveHoldings = [];
    
    // NOT managed by Doctrine - market data from external source
    private ?array $marketData = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $allocationStrategy = [];
    
    // NOT managed by Doctrine - calculated performance metrics
    private ?float $yearToDateReturn = null;
    
    // NOT managed by Doctrine - calculated performance metrics
    private ?float $annualizedReturn = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastRebalanced = null;
    
    // NOT managed by Doctrine - external advisor recommendations
    private array $advisorRecommendations = [];
    
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getPortfolioNumber(): string
    {
        return $this->portfolioNumber;
    }
    
    public function setPortfolioNumber(string $portfolioNumber): self
    {
        $this->portfolioNumber = $portfolioNumber;
        return $this;
    }
    
    public function getTotalValue(): float
    {
        return (float) $this->totalValue;
    }
    
    public function setTotalValue(float $totalValue): self
    {
        $this->totalValue = (string) $totalValue;
        return $this;
    }
    
    public function getCurrency(): string
    {
        return $this->currency;
    }
    
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    
    public function getRiskProfile(): string
    {
        return $this->riskProfile;
    }
    
    public function setRiskProfile(string $riskProfile): self
    {
        $this->riskProfile = $riskProfile;
        return $this;
    }
    
    public function getLiveHoldings(): array
    {
        return $this->liveHoldings;
    }
    
    public function setLiveHoldings(array $liveHoldings): self
    {
        $this->liveHoldings = $liveHoldings;
        return $this;
    }
    
    public function getMarketData(): ?array
    {
        return $this->marketData;
    }
    
    public function setMarketData(?array $marketData): self
    {
        $this->marketData = $marketData;
        return $this;
    }
    
    public function getAllocationStrategy(): array
    {
        return $this->allocationStrategy;
    }
    
    public function setAllocationStrategy(array $allocationStrategy): self
    {
        $this->allocationStrategy = $allocationStrategy;
        return $this;
    }
    
    public function getYearToDateReturn(): ?float
    {
        return $this->yearToDateReturn;
    }
    
    public function setYearToDateReturn(?float $yearToDateReturn): self
    {
        $this->yearToDateReturn = $yearToDateReturn;
        return $this;
    }
    
    public function getAnnualizedReturn(): ?float
    {
        return $this->annualizedReturn;
    }
    
    public function setAnnualizedReturn(?float $annualizedReturn): self
    {
        $this->annualizedReturn = $annualizedReturn;
        return $this;
    }
    
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    
    public function getLastRebalanced(): ?\DateTimeImmutable
    {
        return $this->lastRebalanced;
    }
    
    public function setLastRebalanced(?\DateTimeImmutable $lastRebalanced): self
    {
        $this->lastRebalanced = $lastRebalanced;
        return $this;
    }
    
    public function getAdvisorRecommendations(): array
    {
        return $this->advisorRecommendations;
    }
    
    public function setAdvisorRecommendations(array $advisorRecommendations): self
    {
        $this->advisorRecommendations = $advisorRecommendations;
        return $this;
    }
}
