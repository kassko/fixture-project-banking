<?php

declare(strict_types=1);

namespace App\Service\Consolidation;

use App\Context\UnifiedContext;
use App\DTO\Request\ConsolidationRequest;
use App\DTO\Response\ConsolidationResponse;
use App\Repository\BankAccountRepository;
use App\Repository\CustomerRepository;

class AccountConsolidationService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private BankAccountRepository $bankAccountRepository,
        private ConsolidationCalculator $consolidationCalculator
    ) {
    }

    public function consolidate(ConsolidationRequest $request, UnifiedContext $context): ConsolidationResponse
    {
        // Verify customer exists
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Get accounts
        $accounts = $this->getAccountsData($request, $context);

        // Calculate aggregated balances
        $aggregatedBalances = $this->consolidationCalculator->calculateAggregatedBalances($accounts);

        // Build asset/liability view
        $assetLiabilityView = $this->consolidationCalculator->buildAssetLiabilityView($accounts);

        // Generate statistics
        $statistics = $this->consolidationCalculator->generateStatistics($accounts, $aggregatedBalances);

        $consolidationDate = $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s');

        return new ConsolidationResponse(
            $request->getCustomerId(),
            $accounts,
            $aggregatedBalances,
            $assetLiabilityView,
            $statistics,
            $consolidationDate
        );
    }

    public function getSummary(int $customerId, UnifiedContext $context): array
    {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Get all accounts for customer
        $bankAccounts = $this->bankAccountRepository->findByCustomer($customerId);
        
        $accountsData = [];
        foreach ($bankAccounts as $account) {
            $accountsData[] = $this->mapAccountToArray($account);
        }

        // Calculate quick summary
        $aggregatedBalances = $this->consolidationCalculator->calculateAggregatedBalances($accountsData);
        $statistics = $this->consolidationCalculator->generateStatistics($accountsData, $aggregatedBalances);

        return [
            'customer_id' => $customerId,
            'customer_name' => $customer->getFirstName() . ' ' . $customer->getLastName(),
            'total_accounts' => count($accountsData),
            'net_position' => $aggregatedBalances['net_position'],
            'total_assets' => $aggregatedBalances['total_assets'],
            'total_liabilities' => $aggregatedBalances['total_liabilities'],
            'liquidity_ratio' => $statistics['liquidity_ratio'],
            'summary_date' => $context->getTemporalContext()->getCurrentDateTime()->format('Y-m-d H:i:s'),
        ];
    }

    private function getAccountsData(ConsolidationRequest $request, UnifiedContext $context): array
    {
        $accountsData = [];
        
        // Get accounts based on request
        if ($request->getAccountIds() !== null) {
            // Get specific accounts
            foreach ($request->getAccountIds() as $accountId) {
                $account = $this->bankAccountRepository->find($accountId);
                if ($account && $account->getCustomer()->getId() === $request->getCustomerId()) {
                    if ($request->isIncludeInactiveAccounts() || $account->isActive()) {
                        $accountsData[] = $this->mapAccountToArray($account);
                    }
                }
            }
        } else {
            // Get all accounts for customer
            $bankAccounts = $this->bankAccountRepository->findByCustomer($request->getCustomerId());
            
            foreach ($bankAccounts as $account) {
                if ($request->isIncludeInactiveAccounts() || $account->isActive()) {
                    // Apply consolidation type filter
                    if ($this->matchesConsolidationType($account, $request->getConsolidationType())) {
                        $accountsData[] = $this->mapAccountToArray($account);
                    }
                }
            }
        }

        return $accountsData;
    }

    private function mapAccountToArray($account): array
    {
        return [
            'account_id' => $account->getId(),
            'account_number' => $account->getAccountNumber(),
            'account_type' => $account->getType()->value,
            'balance' => $account->getBalance()->getAmount(),
            'currency' => $account->getCurrency(),
            'status' => $account->isActive() ? 'ACTIVE' : 'INACTIVE',
            'opening_date' => $account->getCreatedAt() ? $account->getCreatedAt()->format('Y-m-d') : null,
        ];
    }

    private function matchesConsolidationType($account, string $consolidationType): bool
    {
        if ($consolidationType === 'ALL') {
            return true;
        }

        $accountType = $account->getType()->value;

        return match ($consolidationType) {
            'CHECKING' => $accountType === 'CHECKING',
            'SAVINGS' => $accountType === 'SAVINGS',
            'INVESTMENT' => in_array($accountType, ['INVESTMENT', 'STOCK', 'BOND']),
            'LOAN' => in_array($accountType, ['LOAN', 'MORTGAGE', 'CREDIT']),
            default => true,
        };
    }
}
