<?php

declare(strict_types=1);

namespace App\Legacy\HybridDataObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Hybrid claim with SOME properties managed by Doctrine, others manually.
 */
#[ORM\Entity]
#[ORM\Table(name: 'hybrid_claims')]
class HybridClaim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100, unique: true)]
    private string $claimNumber;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $claimedAmount;
    
    #[ORM\Column(length: 3)]
    private string $currency = 'EUR';
    
    #[ORM\Column(length: 50)]
    private string $status = 'pending';
    
    // NOT managed by Doctrine - legacy adjuster notes
    private array $adjusterNotes = [];
    
    // NOT managed by Doctrine - external fraud check results
    private ?array $fraudCheckResults = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $metadata = [];
    
    // NOT managed by Doctrine - calculated field
    private ?float $approvalProbability = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $filedAt;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $processedAt = null;
    
    // NOT managed by Doctrine - temporary processing data
    private array $documentsToReview = [];
    
    public function __construct()
    {
        $this->filedAt = new \DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getClaimNumber(): string
    {
        return $this->claimNumber;
    }
    
    public function setClaimNumber(string $claimNumber): self
    {
        $this->claimNumber = $claimNumber;
        return $this;
    }
    
    public function getClaimedAmount(): float
    {
        return (float) $this->claimedAmount;
    }
    
    public function setClaimedAmount(float $claimedAmount): self
    {
        $this->claimedAmount = (string) $claimedAmount;
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
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    
    public function getAdjusterNotes(): array
    {
        return $this->adjusterNotes;
    }
    
    public function setAdjusterNotes(array $adjusterNotes): self
    {
        $this->adjusterNotes = $adjusterNotes;
        return $this;
    }
    
    public function getFraudCheckResults(): ?array
    {
        return $this->fraudCheckResults;
    }
    
    public function setFraudCheckResults(?array $fraudCheckResults): self
    {
        $this->fraudCheckResults = $fraudCheckResults;
        return $this;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
    
    public function getApprovalProbability(): ?float
    {
        return $this->approvalProbability;
    }
    
    public function setApprovalProbability(?float $approvalProbability): self
    {
        $this->approvalProbability = $approvalProbability;
        return $this;
    }
    
    public function getFiledAt(): \DateTimeImmutable
    {
        return $this->filedAt;
    }
    
    public function setFiledAt(\DateTimeImmutable $filedAt): self
    {
        $this->filedAt = $filedAt;
        return $this;
    }
    
    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processedAt;
    }
    
    public function setProcessedAt(?\DateTimeImmutable $processedAt): self
    {
        $this->processedAt = $processedAt;
        return $this;
    }
    
    public function getDocumentsToReview(): array
    {
        return $this->documentsToReview;
    }
    
    public function setDocumentsToReview(array $documentsToReview): self
    {
        $this->documentsToReview = $documentsToReview;
        return $this;
    }
}
