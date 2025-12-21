<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractInsuranceProduct;
use App\Model\Financial\MoneyAmount;
use App\Model\Insurance\Coverage;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Life insurance policy entity - extends AbstractInsuranceProduct.
 */
#[ORM\Entity]
#[ORM\Table(name: 'life_insurance_policies')]
class LifeInsurancePolicy extends AbstractInsuranceProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $policyNumber;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'death_benefit_')]
    private MoneyAmount $deathBenefit;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'cash_value_')]
    private ?MoneyAmount $cashValue = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $startDate;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $maturityDate = null;
    
    #[ORM\Column(length: 50)]
    private string $policyType = 'TERM';
    
    #[ORM\Column(type: Types::JSON)]
    private array $beneficiaries = [];
    
    #[ORM\Column]
    private bool $hasMedicalExam = false;
    
    public function __construct()
    {
        $this->deathBenefit = new MoneyAmount(0.0, 'EUR');
        $this->startDate = new DateTimeImmutable();
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
    
    public function getDeathBenefit(): MoneyAmount
    {
        return $this->deathBenefit;
    }
    
    public function setDeathBenefit(MoneyAmount $deathBenefit): static
    {
        $this->deathBenefit = $deathBenefit;
        return $this;
    }
    
    public function getCashValue(): ?MoneyAmount
    {
        return $this->cashValue;
    }
    
    public function setCashValue(?MoneyAmount $cashValue): static
    {
        $this->cashValue = $cashValue;
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
    
    public function getMaturityDate(): ?DateTimeImmutable
    {
        return $this->maturityDate;
    }
    
    public function setMaturityDate(?DateTimeImmutable $maturityDate): static
    {
        $this->maturityDate = $maturityDate;
        return $this;
    }
    
    public function getPolicyType(): string
    {
        return $this->policyType;
    }
    
    public function setPolicyType(string $policyType): static
    {
        $this->policyType = $policyType;
        return $this;
    }
    
    public function getBeneficiaries(): array
    {
        return $this->beneficiaries;
    }
    
    public function setBeneficiaries(array $beneficiaries): static
    {
        $this->beneficiaries = $beneficiaries;
        return $this;
    }
    
    public function addBeneficiary(array $beneficiary): static
    {
        $this->beneficiaries[] = $beneficiary;
        return $this;
    }
    
    public function hasMedicalExam(): bool
    {
        return $this->hasMedicalExam;
    }
    
    public function setHasMedicalExam(bool $hasMedicalExam): static
    {
        $this->hasMedicalExam = $hasMedicalExam;
        return $this;
    }
    
    public function calculateValue(): MoneyAmount
    {
        return $this->cashValue ?? new MoneyAmount(0.0, 'EUR');
    }
}
