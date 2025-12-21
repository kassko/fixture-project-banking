<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractCustomer;
use App\Enum\CustomerType;
use App\Enum\PremiumLevel;
use App\Traits\MetadataContainerTrait;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Premium customer with exclusive services (level 3 inheritance).
 */
#[ORM\Entity]
#[ORM\Table(name: 'premium_customers')]
class PremiumCustomer extends AbstractCustomer
{
    use MetadataContainerTrait;
    
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
    private string $minimumBalance = '10000.00';
    
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private string $discountRate = '5.00';
    
    public function __construct()
    {
        $this->level = PremiumLevel::BRONZE;
        $this->generateUuid();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getLevel(): PremiumLevel
    {
        return $this->level;
    }
    
    public function setLevel(PremiumLevel $level): static
    {
        $this->level = $level;
        return $this;
    }
    
    public function getPersonalAdvisorName(): ?string
    {
        return $this->personalAdvisorName;
    }
    
    public function setPersonalAdvisorName(?string $personalAdvisorName): static
    {
        $this->personalAdvisorName = $personalAdvisorName;
        return $this;
    }
    
    public function getMinimumBalance(): float
    {
        return (float) $this->minimumBalance;
    }
    
    public function setMinimumBalance(float $minimumBalance): static
    {
        $this->minimumBalance = (string) $minimumBalance;
        return $this;
    }
    
    public function getDiscountRate(): float
    {
        return (float) $this->discountRate;
    }
    
    public function setDiscountRate(float $discountRate): static
    {
        $this->discountRate = (string) $discountRate;
        return $this;
    }
}
