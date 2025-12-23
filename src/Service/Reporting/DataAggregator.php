<?php

declare(strict_types=1);

namespace App\Service\Reporting;

class DataAggregator
{
    public function aggregateCustomerData(int $customerId, ?array $dateRange, ?array $filters): array
    {
        // Simulate data aggregation from various sources
        $transactions = $this->getTransactions($customerId, $dateRange, $filters);
        $accounts = $this->getAccounts($customerId);
        
        return [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'summary' => $this->calculateSummary($transactions, $accounts),
        ];
    }

    public function aggregateFinancialData(int $customerId, ?array $dateRange): array
    {
        $income = $this->calculateIncome($customerId, $dateRange);
        $expenses = $this->calculateExpenses($customerId, $dateRange);
        $balance = $this->getAccountBalance($customerId);
        
        return [
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $balance,
            'net_cash_flow' => $income - $expenses,
        ];
    }

    private function getTransactions(int $customerId, ?array $dateRange, ?array $filters): array
    {
        // Simulate transaction retrieval
        $transactions = [];
        $count = rand(5, 15);
        
        for ($i = 0; $i < $count; $i++) {
            $transactions[] = [
                'id' => rand(10000, 99999),
                'date' => date('Y-m-d', time() - rand(0, 30) * 86400),
                'amount' => rand(10, 500),
                'type' => ['debit', 'credit'][rand(0, 1)],
                'description' => 'Transaction ' . ($i + 1),
            ];
        }
        
        return $transactions;
    }

    private function getAccounts(int $customerId): array
    {
        // NOTE: Simulated account data for demonstration purposes.
        // Account numbers are clearly fake examples (not real IBANs).
        return [
            [
                'account_number' => 'FR7612345678901234567890123',
                'type' => 'checking',
                'balance' => rand(1000, 10000),
                'currency' => 'EUR',
            ],
            [
                'account_number' => 'FR7698765432109876543210987',
                'type' => 'savings',
                'balance' => rand(5000, 50000),
                'currency' => 'EUR',
            ],
        ];
    }

    private function calculateSummary(array $transactions, array $accounts): array
    {
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($transactions as $tx) {
            if ($tx['type'] === 'debit') {
                $totalDebit += $tx['amount'];
            } else {
                $totalCredit += $tx['amount'];
            }
        }
        
        $totalBalance = array_sum(array_column($accounts, 'balance'));
        
        return [
            'total_transactions' => count($transactions),
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'total_balance' => $totalBalance,
        ];
    }

    private function calculateIncome(int $customerId, ?array $dateRange): float
    {
        // Simulate income calculation
        return (float) rand(3000, 6000);
    }

    private function calculateExpenses(int $customerId, ?array $dateRange): float
    {
        // Simulate expenses calculation
        return (float) rand(2000, 4500);
    }

    private function getAccountBalance(int $customerId): float
    {
        // Simulate account balance retrieval
        return (float) rand(5000, 20000);
    }
}
