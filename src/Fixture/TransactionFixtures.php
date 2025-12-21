<?php

declare(strict_types=1);

namespace App\Fixture;

use App\Entity\Transaction;
use App\Enum\TransactionType;
use App\Model\Financial\MoneyAmount;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Transaction fixtures with realistic transaction data.
 */
class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Create various transactions
        $transaction1 = new Transaction();
        $transaction1
            ->setTransactionId('TXN-2024-001')
            ->setType(TransactionType::DEPOSIT)
            ->setAmount(new MoneyAmount(2000.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-01-15 09:00:00'))
            ->setStatus('completed')
            ->setDescription('Salary deposit')
            ->setReference('SAL-JAN-2024');
        
        $transaction1->updateTimestamps();
        
        $manager->persist($transaction1);
        
        $transaction2 = new Transaction();
        $transaction2
            ->setTransactionId('TXN-2024-002')
            ->setType(TransactionType::WITHDRAWAL)
            ->setAmount(new MoneyAmount(500.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-01-16 14:30:00'))
            ->setStatus('completed')
            ->setDescription('ATM withdrawal')
            ->setReference('ATM-75001-001');
        
        $transaction2->updateTimestamps();
        
        $manager->persist($transaction2);
        
        $transaction3 = new Transaction();
        $transaction3
            ->setTransactionId('TXN-2024-003')
            ->setType(TransactionType::TRANSFER)
            ->setAmount(new MoneyAmount(10000.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-01-20 11:15:00'))
            ->setStatus('completed')
            ->setDescription('Transfer to savings account')
            ->setReference('TRF-INT-001');
        
        $transaction3->updateTimestamps();
        
        $manager->persist($transaction3);
        
        $transaction4 = new Transaction();
        $transaction4
            ->setTransactionId('TXN-2024-004')
            ->setType(TransactionType::PAYMENT)
            ->setAmount(new MoneyAmount(1200.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-02-01 10:00:00'))
            ->setStatus('completed')
            ->setDescription('Insurance premium payment')
            ->setReference('INS-PREM-2024-001');
        
        $transaction4->updateTimestamps();
        
        $manager->persist($transaction4);
        
        $transaction5 = new Transaction();
        $transaction5
            ->setTransactionId('TXN-2024-005')
            ->setType(TransactionType::FEE)
            ->setAmount(new MoneyAmount(15.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-02-28 23:59:00'))
            ->setStatus('completed')
            ->setDescription('Monthly account maintenance fee')
            ->setReference('FEE-MAINT-FEB');
        
        $transaction5->updateTimestamps();
        
        $manager->persist($transaction5);
        
        $transaction6 = new Transaction();
        $transaction6
            ->setTransactionId('TXN-2024-006')
            ->setType(TransactionType::DEPOSIT)
            ->setAmount(new MoneyAmount(5000.00, 'EUR'))
            ->setTransactionDate(new DateTimeImmutable('2024-03-05 12:30:00'))
            ->setStatus('pending')
            ->setDescription('Check deposit')
            ->setReference('CHK-001234');
        
        $transaction6->updateTimestamps();
        
        $manager->persist($transaction6);
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}
