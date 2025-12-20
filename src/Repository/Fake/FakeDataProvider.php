<?php

declare(strict_types=1);

namespace App\Repository\Fake;

use App\Enum\AccountType;
use App\Enum\CustomerType;
use App\Enum\PolicyStatus;
use App\Enum\TransactionType;

/**
 * Provides fake in-memory data for testing purposes.
 * Simulates a database with pre-filled realistic data.
 */
class FakeDataProvider
{
    /**
     * @var array<string, array>
     */
    private array $customers = [];
    
    /**
     * @var array<string, array>
     */
    private array $accounts = [];
    
    /**
     * @var array<string, array>
     */
    private array $transactions = [];
    
    /**
     * @var array<string, array>
     */
    private array $policies = [];
    
    public function __construct()
    {
        $this->initializeData();
    }
    
    private function initializeData(): void
    {
        // Initialize customers
        $this->customers = [
            'CUST001' => [
                'id' => 'CUST001',
                'customerNumber' => 'CUST001',
                'firstName' => 'Jean',
                'lastName' => 'Dupont',
                'type' => CustomerType::INDIVIDUAL->value,
                'email' => 'jean.dupont@email.com',
                'phone' => '+33612345678',
                'isActive' => true,
                'createdAt' => '2023-01-15 10:30:00',
            ],
            'CUST002' => [
                'id' => 'CUST002',
                'customerNumber' => 'CUST002',
                'firstName' => 'Marie',
                'lastName' => 'Martin',
                'type' => CustomerType::INDIVIDUAL->value,
                'email' => 'marie.martin@email.com',
                'phone' => '+33687654321',
                'isActive' => true,
                'createdAt' => '2023-02-20 14:15:00',
            ],
            'CUST003' => [
                'id' => 'CUST003',
                'customerNumber' => 'CUST003',
                'firstName' => 'Tech Solutions',
                'lastName' => 'SARL',
                'type' => CustomerType::BUSINESS->value,
                'email' => 'contact@techsolutions.fr',
                'phone' => '+33145678900',
                'isActive' => true,
                'createdAt' => '2023-03-10 09:00:00',
            ],
        ];
        
        // Initialize accounts
        $this->accounts = [
            'ACC001' => [
                'id' => 'ACC001',
                'accountNumber' => 'FR7612345678901234567890123',
                'customerId' => 'CUST001',
                'type' => AccountType::CHECKING->value,
                'balance' => 15000.50,
                'currency' => 'EUR',
                'isActive' => true,
            ],
            'ACC002' => [
                'id' => 'ACC002',
                'accountNumber' => 'FR7698765432109876543210987',
                'customerId' => 'CUST001',
                'type' => AccountType::SAVINGS->value,
                'balance' => 50000.00,
                'currency' => 'EUR',
                'isActive' => true,
            ],
            'ACC003' => [
                'id' => 'ACC003',
                'accountNumber' => 'FR7611111111112222222222222',
                'customerId' => 'CUST002',
                'type' => AccountType::CHECKING->value,
                'balance' => 8500.75,
                'currency' => 'EUR',
                'isActive' => true,
            ],
        ];
        
        // Initialize transactions
        $this->transactions = [
            'TXN001' => [
                'id' => 'TXN001',
                'transactionId' => 'TXN001',
                'accountId' => 'ACC001',
                'type' => TransactionType::DEPOSIT->value,
                'amount' => 2000.00,
                'currency' => 'EUR',
                'status' => 'completed',
                'description' => 'Salary deposit',
                'transactionDate' => '2024-01-15 09:00:00',
            ],
            'TXN002' => [
                'id' => 'TXN002',
                'transactionId' => 'TXN002',
                'accountId' => 'ACC001',
                'type' => TransactionType::WITHDRAWAL->value,
                'amount' => 500.00,
                'currency' => 'EUR',
                'status' => 'completed',
                'description' => 'ATM withdrawal',
                'transactionDate' => '2024-01-16 14:30:00',
            ],
            'TXN003' => [
                'id' => 'TXN003',
                'transactionId' => 'TXN003',
                'accountId' => 'ACC002',
                'type' => TransactionType::TRANSFER->value,
                'amount' => 10000.00,
                'currency' => 'EUR',
                'status' => 'completed',
                'description' => 'Transfer from checking',
                'transactionDate' => '2024-01-20 11:15:00',
            ],
        ];
        
        // Initialize policies
        $this->policies = [
            'POL001' => [
                'id' => 'POL001',
                'policyNumber' => 'INS-2024-001',
                'customerId' => 'CUST001',
                'status' => PolicyStatus::ACTIVE->value,
                'premium' => 1200.00,
                'currency' => 'EUR',
                'effectiveDate' => '2024-01-01',
                'expirationDate' => '2025-01-01',
            ],
            'POL002' => [
                'id' => 'POL002',
                'policyNumber' => 'INS-2024-002',
                'customerId' => 'CUST002',
                'status' => PolicyStatus::ACTIVE->value,
                'premium' => 800.00,
                'currency' => 'EUR',
                'effectiveDate' => '2024-02-01',
                'expirationDate' => '2025-02-01',
            ],
        ];
    }
    
    public function getCustomer(string $id): ?array
    {
        return $this->customers[$id] ?? null;
    }
    
    /**
     * @return array<string, array>
     */
    public function getAllCustomers(): array
    {
        return $this->customers;
    }
    
    public function getAccount(string $id): ?array
    {
        return $this->accounts[$id] ?? null;
    }
    
    /**
     * @return array<string, array>
     */
    public function getAccountsByCustomer(string $customerId): array
    {
        return array_filter($this->accounts, fn($account) => $account['customerId'] === $customerId);
    }
    
    public function getTransaction(string $id): ?array
    {
        return $this->transactions[$id] ?? null;
    }
    
    /**
     * @return array<string, array>
     */
    public function getTransactionsByAccount(string $accountId): array
    {
        return array_filter($this->transactions, fn($txn) => $txn['accountId'] === $accountId);
    }
    
    public function getPolicy(string $id): ?array
    {
        return $this->policies[$id] ?? null;
    }
    
    /**
     * @return array<string, array>
     */
    public function getPoliciesByCustomer(string $customerId): array
    {
        return array_filter($this->policies, fn($policy) => $policy['customerId'] === $customerId);
    }
}
