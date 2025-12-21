<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\CustomerType;
use App\Enum\PremiumLevel;
use App\Traits\SoftDeletableTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * VIP customer with top-tier exclusive services (level 4 inheritance).
 */
#[ORM\Entity]
#[ORM\Table(name: 'vip_customers')]
class VIPCustomer extends PremiumCustomer
{
    use SoftDeletableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 100)]
    protected string $firstName;
    
    #[ORM\Column(length: 100)]
    protected string $lastName;
    
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    protected ?\DateTimeImmutable $birthDate = null;
    
    #[ORM\Column(length: 50, unique: true)]
    protected string $customerNumber;
    
    #[ORM\Column(type: 'string', enumType: CustomerType::class)]
    protected CustomerType $type;
    
    #[ORM\Column]
    protected bool $isActive = true;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uuid = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $createdBy = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $updatedBy = null;
    
    #[ORM\Column]
    private int $version = 1;
    
    #[ORM\Column(type: 'string', enumType: PremiumLevel::class)]
    private PremiumLevel $level;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $personalAdvisorName = null;
    
    #[ORM\Column(type: Types::JSON)]
    private array $metadata = [];
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $minimumBalance = '100000.00';
    
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $discountRate = '15.00';
    
    #[ORM\Column(type: Types::JSON)]
    private array $exclusiveServices = [];
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $privateBankerName = null;
    
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $privateBankerEmail = null;
    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $privateBankerPhone = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;
    
    #[ORM\Column]
    private bool $isDeleted = false;
    
    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private string $annualFee = '5000.00';
    
    #[ORM\Column]
    private bool $hasConciergeService = true;
    
    public function __construct()
    {
        parent::__construct();
        $this->level = PremiumLevel::PLATINUM;
        $this->minimumBalance = '100000.00';
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return array<string>
     */
    public function getExclusiveServices(): array
    {
        return $this->exclusiveServices;
    }
    
    /**
     * @param array<string> $exclusiveServices
     */
    public function setExclusiveServices(array $exclusiveServices): static
    {
        $this->exclusiveServices = $exclusiveServices;
        return $this;
    }
    
    public function addExclusiveService(string $service): static
    {
        if (!in_array($service, $this->exclusiveServices, true)) {
            $this->exclusiveServices[] = $service;
        }
        return $this;
    }
    
    public function getPrivateBankerName(): ?string
    {
        return $this->privateBankerName;
    }
    
    public function setPrivateBankerName(?string $privateBankerName): static
    {
        $this->privateBankerName = $privateBankerName;
        return $this;
    }
    
    public function getPrivateBankerEmail(): ?string
    {
        return $this->privateBankerEmail;
    }
    
    public function setPrivateBankerEmail(?string $privateBankerEmail): static
    {
        $this->privateBankerEmail = $privateBankerEmail;
        return $this;
    }
    
    public function getPrivateBankerPhone(): ?string
    {
        return $this->privateBankerPhone;
    }
    
    public function setPrivateBankerPhone(?string $privateBankerPhone): static
    {
        $this->privateBankerPhone = $privateBankerPhone;
        return $this;
    }
    
    public function getAnnualFee(): float
    {
        return (float) $this->annualFee;
    }
    
    public function setAnnualFee(float $annualFee): static
    {
        $this->annualFee = (string) $annualFee;
        return $this;
    }
    
    public function hasConciergeService(): bool
    {
        return $this->hasConciergeService;
    }
    
    public function setHasConciergeService(bool $hasConciergeService): static
    {
        $this->hasConciergeService = $hasConciergeService;
        return $this;
    }
}
