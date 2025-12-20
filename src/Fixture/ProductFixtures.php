<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\BankAccount;
use App\Entity\InsurancePolicy;
use App\Enum\AccountType;
use App\Enum\PolicyStatus;
use App\Model\Financial\Deductible;
use App\Model\Financial\MoneyAmount;
use App\Model\Insurance\Coverage;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Product fixtures (accounts and insurance policies).
 */
class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Note: In a real scenario, we would fetch customers from the database
        // For this fixture, we'll create standalone examples
        
        // Create bank accounts
        $account1 = new BankAccount();
        $account1
            ->setAccountNumber('FR7612345678901234567890123')
            ->setType(AccountType::CHECKING)
            ->setBalance(new MoneyAmount(15000.50, 'EUR'))
            ->setIsActive(true);
        
        $account1->updateTimestamps();
        
        $manager->persist($account1);
        
        $account2 = new BankAccount();
        $account2
            ->setAccountNumber('FR7698765432109876543210987')
            ->setType(AccountType::SAVINGS)
            ->setBalance(new MoneyAmount(50000.00, 'EUR'))
            ->setIsActive(true);
        
        $account2->updateTimestamps();
        
        $manager->persist($account2);
        
        $account3 = new BankAccount();
        $account3
            ->setAccountNumber('FR7611111111112222222222222')
            ->setType(AccountType::INVESTMENT)
            ->setBalance(new MoneyAmount(125000.00, 'EUR'))
            ->setIsActive(true);
        
        $account3->updateTimestamps();
        
        $manager->persist($account3);
        
        // Create insurance policies
        $policy1 = new InsurancePolicy();
        $policy1
            ->setPolicyNumber('INS-2024-001')
            ->setStatus(PolicyStatus::ACTIVE)
            ->setEffectiveDate(new DateTimeImmutable('2024-01-01'))
            ->setExpirationDate(new DateTimeImmutable('2025-01-01'))
            ->setPremium('1200.00')
            ->setCurrency('EUR');
        
        $policy1->updateTimestamps();
        
        // Add coverages
        $deductible1 = new Deductible(new MoneyAmount(0, 'EUR'), 'none');
        $coverage1 = new Coverage(
            'death_benefit',
            new MoneyAmount(500000.00, 'EUR'),
            $deductible1,
            ['suicide_first_year', 'war'],
            true
        );
        
        $deductible2 = new Deductible(new MoneyAmount(1000.00, 'EUR'), 'per_claim');
        $coverage2 = new Coverage(
            'disability',
            new MoneyAmount(250000.00, 'EUR'),
            $deductible2,
            ['pre_existing_conditions'],
            true
        );
        
        $policy1->addCoverage($coverage1);
        $policy1->addCoverage($coverage2);
        
        $manager->persist($policy1);
        
        $policy2 = new InsurancePolicy();
        $policy2
            ->setPolicyNumber('INS-2024-002')
            ->setStatus(PolicyStatus::ACTIVE)
            ->setEffectiveDate(new DateTimeImmutable('2024-02-01'))
            ->setExpirationDate(new DateTimeImmutable('2025-02-01'))
            ->setPremium('800.00')
            ->setCurrency('EUR');
        
        $policy2->updateTimestamps();
        
        $deductible3 = new Deductible(new MoneyAmount(500.00, 'EUR'), 'per_claim');
        $coverage3 = new Coverage(
            'dwelling',
            new MoneyAmount(350000.00, 'EUR'),
            $deductible3,
            ['flood', 'earthquake'],
            true
        );
        
        $deductible4 = new Deductible(new MoneyAmount(250.00, 'EUR'), 'per_claim');
        $coverage4 = new Coverage(
            'personal_property',
            new MoneyAmount(100000.00, 'EUR'),
            $deductible4,
            ['wear_and_tear'],
            true
        );
        
        $policy2->addCoverage($coverage3);
        $policy2->addCoverage($coverage4);
        
        $manager->persist($policy2);
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
