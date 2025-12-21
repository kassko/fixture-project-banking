<?php

declare(strict_types=1);

namespace App\Service\Consolidation;

class ConsolidationCalculator
{
    /**
     * Calculate aggregated balances across multiple accounts.
     * 
     * @param array $accounts Array of account data with keys: balance, currency, account_type
     * @return array Aggregated balances with total_assets, total_liabilities, net_position, etc.
     */
    public function calculateAggregatedBalances(array $accounts): array
    {
        $totalAssets = 0.0;
        $totalLiabilities = 0.0;
        $totalAvailable = 0.0;
        $byAccountType = [];
        $byCurrency = [];

        foreach ($accounts as $account) {
            $balance = $account['balance'];
            $currency = $account['currency'];
            $accountType = $account['account_type'];

            // Aggregate by type
            if (!isset($byAccountType[$accountType])) {
                $byAccountType[$accountType] = [
                    'count' => 0,
                    'total_balance' => 0.0,
                ];
            }
            $byAccountType[$accountType]['count']++;
            $byAccountType[$accountType]['total_balance'] += $balance;

            // Aggregate by currency
            if (!isset($byCurrency[$currency])) {
                $byCurrency[$currency] = [
                    'total' => 0.0,
                    'accounts' => 0,
                ];
            }
            $byCurrency[$currency]['total'] += $balance;
            $byCurrency[$currency]['accounts']++;

            // Calculate assets/liabilities
            if ($balance >= 0) {
                $totalAssets += $balance;
            } else {
                $totalLiabilities += abs($balance);
            }

            $totalAvailable += $balance;
        }

        return [
            'total_assets' => round($totalAssets, 2),
            'total_liabilities' => round($totalLiabilities, 2),
            'net_position' => round($totalAssets - $totalLiabilities, 2),
            'total_available' => round($totalAvailable, 2),
            'by_account_type' => $byAccountType,
            'by_currency' => $byCurrency,
        ];
    }

    public function buildAssetLiabilityView(array $accounts): array
    {
        $assets = [];
        $liabilities = [];

        foreach ($accounts as $account) {
            $accountData = [
                'account_id' => $account['account_id'],
                'account_number' => $account['account_number'],
                'account_type' => $account['account_type'],
                'balance' => $account['balance'],
                'currency' => $account['currency'],
            ];

            if ($account['balance'] >= 0) {
                $assets[] = $accountData;
            } else {
                $liabilities[] = $accountData;
            }
        }

        // Sort by balance (descending for assets, ascending for liabilities)
        usort($assets, fn($a, $b) => $b['balance'] <=> $a['balance']);
        usort($liabilities, fn($a, $b) => $a['balance'] <=> $b['balance']);

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'asset_count' => count($assets),
            'liability_count' => count($liabilities),
        ];
    }

    public function generateStatistics(array $accounts, array $aggregatedBalances): array
    {
        if (empty($accounts)) {
            return [
                'account_count' => 0,
                'average_balance' => 0.0,
                'highest_balance' => 0.0,
                'lowest_balance' => 0.0,
                'active_accounts' => 0,
                'inactive_accounts' => 0,
            ];
        }

        $balances = array_column($accounts, 'balance');
        $activeCount = count(array_filter($accounts, fn($a) => $a['status'] === 'ACTIVE'));

        return [
            'account_count' => count($accounts),
            'average_balance' => round(array_sum($balances) / count($balances), 2),
            'highest_balance' => round(max($balances), 2),
            'lowest_balance' => round(min($balances), 2),
            'active_accounts' => $activeCount,
            'inactive_accounts' => count($accounts) - $activeCount,
            'net_worth' => $aggregatedBalances['net_position'],
            'liquidity_ratio' => $this->calculateLiquidityRatio($accounts),
        ];
    }

    /**
     * Calculate liquidity ratio - percentage of liquid assets to total assets.
     * Liquid assets are defined as CHECKING and SAVINGS account balances.
     * Formula: (Liquid Assets / Total Assets) * 100
     * 
     * @param array $accounts Array of account data
     * @return float Liquidity ratio as percentage (0-100)
     */
    private function calculateLiquidityRatio(array $accounts): float
    {
        $liquidAssets = 0.0;
        $totalAssets = 0.0;

        foreach ($accounts as $account) {
            if ($account['balance'] > 0) {
                $totalAssets += $account['balance'];
                
                // Checking and savings accounts are considered liquid
                if (in_array($account['account_type'], ['CHECKING', 'SAVINGS'])) {
                    $liquidAssets += $account['balance'];
                }
            }
        }

        if ($totalAssets === 0.0) {
            return 0.0;
        }

        return round(($liquidAssets / $totalAssets) * 100, 2);
    }
}
