<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Context\UnifiedContext;
use App\DataSource\HistoricalRatesDataSource;
use App\DataSource\LegacyCustomerDataSource;
use App\DTO\Request\LoanSimulationRequest;
use App\DTO\Response\LoanScenario;
use App\DTO\Response\LoanSimulationResponse;
use App\Entity\Customer;
use App\Legacy\DataObject\LegacyRiskAssessment;
use App\Repository\CustomerRepository;
use App\Service\Simulation\PricingEngine\RateCalculator;

class LoanSimulationService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private LegacyCustomerDataSource $legacyCustomerDataSource,
        private HistoricalRatesDataSource $historicalRatesDataSource,
        private RateCalculator $rateCalculator
    ) {
    }

    public function simulate(LoanSimulationRequest $request, UnifiedContext $context): LoanSimulationResponse
    {
        // Get customer data
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Get customer profile and risk data
        $customerProfile = $this->buildCustomerProfile($customer, $context);
        
        // Get base rate from historical data
        $baseRate = $this->getBaseRate($request->getPurpose());
        
        // Get configuration from context
        $tenantConfig = $context->getTenantContext()->getConfiguration();
        $brandConfig = $context->getBrandContext()->getConfiguration();
        $period = $context->getTemporalContext()->getPeriod() ?? 'regular';

        // Generate multiple scenarios
        $scenarios = $this->generateScenarios(
            $request,
            $customerProfile,
            $baseRate,
            $brandConfig,
            $period
        );

        // Determine recommended scenario
        $recommendedScenario = $this->determineRecommendedScenario($scenarios, $customerProfile);

        return new LoanSimulationResponse(
            $request->getCustomerId(),
            $request->getAmount(),
            $request->getCurrency(),
            $request->getPurpose(),
            $scenarios,
            $customerProfile,
            $recommendedScenario
        );
    }

    private function buildCustomerProfile(Customer $customer, UnifiedContext $context): array
    {
        // Determine customer type
        $customerType = 'individual';
        $className = get_class($customer);
        if (str_contains($className, 'Premium')) {
            $customerType = 'premium';
        } elseif (str_contains($className, 'Corporate')) {
            $customerType = 'corporate';
        }

        // Simulate credit score and seniority
        $creditScore = rand(50, 95);
        $customerSeniority = rand(1, 10);

        return [
            'customer_type' => $customerType,
            'credit_score' => $creditScore,
            'seniority_years' => $customerSeniority,
            'customer_number' => $customer->getCustomerNumber(),
        ];
    }

    private function getBaseRate(string $purpose): float
    {
        // Get market rates (could use HistoricalRatesDataSource)
        return match ($purpose) {
            'HOME' => 3.5,
            'AUTO' => 4.2,
            'PERSONAL' => 5.5,
            default => 5.0,
        };
    }

    private function generateScenarios(
        LoanSimulationRequest $request,
        array $customerProfile,
        float $baseRate,
        array $brandConfig,
        string $period
    ): array {
        $scenarios = [];
        $durations = [
            $request->getPreferredDuration(),
            (int) ($request->getPreferredDuration() * 0.75),
            (int) ($request->getPreferredDuration() * 1.5),
        ];

        $brandType = $brandConfig['type'] ?? 'standard';

        foreach ($durations as $index => $duration) {
            if ($duration < 12) {
                $duration = 12;
            }

            // Calculate rate
            $rateResult = $this->rateCalculator->calculateLoanRate(
                $baseRate,
                $customerProfile['customer_type'],
                $customerProfile['credit_score'],
                $customerProfile['seniority_years'],
                $brandType,
                $period
            );

            $finalRate = $rateResult['final_rate'];
            $adjustments = $rateResult['adjustments'];

            // Calculate payment
            $monthlyPayment = $this->rateCalculator->calculateMonthlyPayment(
                $request->getAmount(),
                $finalRate,
                $duration
            );

            $totalCost = $this->rateCalculator->calculateTotalCost($monthlyPayment, $duration);
            $totalInterest = $totalCost - $request->getAmount();

            $scenarioName = match ($index) {
                0 => 'Standard',
                1 => 'Court terme',
                2 => 'Long terme',
                default => 'Scenario ' . ($index + 1),
            };

            $scenarios[] = new LoanScenario(
                $scenarioName,
                $duration,
                $monthlyPayment,
                $totalCost,
                $finalRate,
                $totalInterest,
                $adjustments
            );
        }

        return $scenarios;
    }

    private function determineRecommendedScenario(array $scenarios, array $customerProfile): string
    {
        // Recommend based on credit score
        if ($customerProfile['credit_score'] >= 75) {
            return 'Long terme'; // Lower monthly payment
        }
        
        return 'Standard';
    }
}
