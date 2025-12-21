<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractInsuranceProduct;
use App\Model\Common\Address;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Home insurance policy entity - extends AbstractInsuranceProduct.
 */
#[ORM\Entity]
#[ORM\Table(name: 'home_insurance_policies')]
class HomeInsurancePolicy extends AbstractInsuranceProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $policyNumber;
    
    #[ORM\Embedded(class: Address::class, columnPrefix: 'property_')]
    private Address $propertyAddress;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'building_coverage_')]
    private MoneyAmount $buildingCoverage;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'contents_coverage_')]
    private MoneyAmount $contentsCoverage;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $startDate;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $endDate;
    
    #[ORM\Column(length: 50)]
    private string $propertyType = 'HOUSE';
    
    #[ORM\Column]
    private int $constructionYear;
    
    #[ORM\Column(type: Types::JSON)]
    private array $protectionFeatures = [];
    
    public function __construct()
    {
        $this->propertyAddress = new Address();
        $this->buildingCoverage = new MoneyAmount(0.0, 'EUR');
        $this->contentsCoverage = new MoneyAmount(0.0, 'EUR');
        $this->startDate = new DateTimeImmutable();
        $this->endDate = new DateTimeImmutable('+1 year');
        $this->constructionYear = (int) date('Y');
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getPolicyNumber(): string
    {
        return $this->policyNumber;
    }
    
    public function setPolicyNumber(string $policyNumber): static
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }
    
    public function getPropertyAddress(): Address
    {
        return $this->propertyAddress;
    }
    
    public function setPropertyAddress(Address $propertyAddress): static
    {
        $this->propertyAddress = $propertyAddress;
        return $this;
    }
    
    public function getBuildingCoverage(): MoneyAmount
    {
        return $this->buildingCoverage;
    }
    
    public function setBuildingCoverage(MoneyAmount $buildingCoverage): static
    {
        $this->buildingCoverage = $buildingCoverage;
        return $this;
    }
    
    public function getContentsCoverage(): MoneyAmount
    {
        return $this->contentsCoverage;
    }
    
    public function setContentsCoverage(MoneyAmount $contentsCoverage): static
    {
        $this->contentsCoverage = $contentsCoverage;
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
    
    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }
    
    public function setEndDate(DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getPropertyType(): string
    {
        return $this->propertyType;
    }
    
    public function setPropertyType(string $propertyType): static
    {
        $this->propertyType = $propertyType;
        return $this;
    }
    
    public function getConstructionYear(): int
    {
        return $this->constructionYear;
    }
    
    public function setConstructionYear(int $constructionYear): static
    {
        $this->constructionYear = $constructionYear;
        return $this;
    }
    
    public function getProtectionFeatures(): array
    {
        return $this->protectionFeatures;
    }
    
    public function setProtectionFeatures(array $protectionFeatures): static
    {
        $this->protectionFeatures = $protectionFeatures;
        return $this;
    }
    
    public function addProtectionFeature(string $feature): static
    {
        if (!in_array($feature, $this->protectionFeatures, true)) {
            $this->protectionFeatures[] = $feature;
        }
        return $this;
    }
    
    public function calculateValue(): MoneyAmount
    {
        // Total coverage = building + contents
        $total = $this->buildingCoverage->getAmount() + $this->contentsCoverage->getAmount();
        return new MoneyAmount($total, $this->buildingCoverage->getCurrency());
    }
}
