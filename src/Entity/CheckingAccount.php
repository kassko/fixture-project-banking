<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractBankProduct;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Checking account entity - extends AbstractBankProduct.
 */
#[ORM\Entity]
#[ORM\Table(name: 'checking_accounts')]
class CheckingAccount extends AbstractBankProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $accountNumber;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'balance_')]
    private MoneyAmount $balance;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'overdraft_limit_')]
    private ?MoneyAmount $overdraftLimit = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $openingDate;
    
    #[ORM\Column]
    private bool $hasDebitCard = true;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'monthly_fee_')]
    private ?MoneyAmount $monthlyFee = null;
    
    #[ORM\Column]
    private int $freeChecksPerMonth = 0;
    
    public function __construct()
    {
        $this->balance = new MoneyAmount(0.0, 'EUR');
        $this->openingDate = new DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
    
    public function setAccountNumber(string $accountNumber): static
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }
    
    public function getBalance(): MoneyAmount
    {
        return $this->balance;
    }
    
    public function setBalance(MoneyAmount $balance): static
    {
        $this->balance = $balance;
        return $this;
    }
    
    public function getOverdraftLimit(): ?MoneyAmount
    {
        return $this->overdraftLimit;
    }
    
    public function setOverdraftLimit(?MoneyAmount $overdraftLimit): static
    {
        $this->overdraftLimit = $overdraftLimit;
        return $this;
    }
    
    public function getOpeningDate(): DateTimeImmutable
    {
        return $this->openingDate;
    }
    
    public function setOpeningDate(DateTimeImmutable $openingDate): static
    {
        $this->openingDate = $openingDate;
        return $this;
    }
    
    public function hasDebitCard(): bool
    {
        return $this->hasDebitCard;
    }
    
    public function setHasDebitCard(bool $hasDebitCard): static
    {
        $this->hasDebitCard = $hasDebitCard;
        return $this;
    }
    
    public function getMonthlyFee(): ?MoneyAmount
    {
        return $this->monthlyFee;
    }
    
    public function setMonthlyFee(?MoneyAmount $monthlyFee): static
    {
        $this->monthlyFee = $monthlyFee;
        return $this;
    }
    
    public function getFreeChecksPerMonth(): int
    {
        return $this->freeChecksPerMonth;
    }
    
    public function setFreeChecksPerMonth(int $freeChecksPerMonth): static
    {
        $this->freeChecksPerMonth = $freeChecksPerMonth;
        return $this;
    }
    
    public function calculateValue(): MoneyAmount
    {
        return $this->balance;
    }
}
