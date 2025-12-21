<?php

declare(strict_types=1);

namespace App\Entity;

use App\Abstract\AbstractBankProduct;
use App\Model\Financial\InterestRate;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Savings account entity - extends AbstractBankProduct.
 */
#[ORM\Entity]
#[ORM\Table(name: 'savings_accounts')]
class SavingsAccount extends AbstractBankProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 50, unique: true)]
    private string $accountNumber;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'balance_')]
    private MoneyAmount $balance;
    
    #[ORM\Embedded(class: InterestRate::class, columnPrefix: 'interest_')]
    private ?InterestRate $interestRate = null;
    
    #[ORM\Embedded(class: MoneyAmount::class, columnPrefix: 'minimum_balance_')]
    private ?MoneyAmount $minimumBalance = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $openingDate;
    
    #[ORM\Column]
    private int $withdrawalsPerMonth = 0;
    
    #[ORM\Column]
    private int $maxWithdrawalsPerMonth = 6;
    
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
    
    public function getInterestRate(): ?InterestRate
    {
        return $this->interestRate;
    }
    
    public function setInterestRate(?InterestRate $interestRate): static
    {
        $this->interestRate = $interestRate;
        return $this;
    }
    
    public function getMinimumBalance(): ?MoneyAmount
    {
        return $this->minimumBalance;
    }
    
    public function setMinimumBalance(?MoneyAmount $minimumBalance): static
    {
        $this->minimumBalance = $minimumBalance;
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
    
    public function getWithdrawalsPerMonth(): int
    {
        return $this->withdrawalsPerMonth;
    }
    
    public function setWithdrawalsPerMonth(int $withdrawalsPerMonth): static
    {
        $this->withdrawalsPerMonth = $withdrawalsPerMonth;
        return $this;
    }
    
    public function getMaxWithdrawalsPerMonth(): int
    {
        return $this->maxWithdrawalsPerMonth;
    }
    
    public function setMaxWithdrawalsPerMonth(int $maxWithdrawalsPerMonth): static
    {
        $this->maxWithdrawalsPerMonth = $maxWithdrawalsPerMonth;
        return $this;
    }
    
    public function calculateValue(): MoneyAmount
    {
        // Calculate with accrued interest
        return $this->balance;
    }
}
