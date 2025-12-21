<?php

declare(strict_types=1);

namespace App\Service\Simulation;

use App\Context\UnifiedContext;
use App\DTO\Request\InsuranceQuoteRequest;
use App\DTO\Response\InsuranceFormula;
use App\DTO\Response\InsuranceQuoteResponse;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Service\Simulation\PricingEngine\CampaignDiscountApplier;
use App\Service\Simulation\PricingEngine\RiskAdjustmentStrategy;

class InsuranceQuoteService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private RiskAdjustmentStrategy $riskAdjustmentStrategy,
        private CampaignDiscountApplier $campaignDiscountApplier
    ) {
    }

    public function getQuote(InsuranceQuoteRequest $request, UnifiedContext $context): InsuranceQuoteResponse
    {
        // Get customer data
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Build risk profile
        $riskProfile = $this->buildRiskProfile($customer);
        
        // Get campaigns and period
        $activeCampaigns = $context->getCampaignContext()->getActiveCampaigns();
        $period = $context->getTemporalContext()->getPeriod() ?? 'regular';

        // Generate formulas
        $formulas = $this->generateFormulas(
            $request,
            $riskProfile,
            $activeCampaigns,
            $period
        );

        // Determine recommended formula
        $recommendedFormula = $this->determineRecommendedFormula($formulas, $riskProfile);

        return new InsuranceQuoteResponse(
            $request->getCustomerId(),
            $request->getInsuranceType(),
            $formulas,
            $riskProfile,
            $recommendedFormula
        );
    }

    private function buildRiskProfile(Customer $customer): array
    {
        // Determine customer type
        $customerType = 'individual';
        $className = get_class($customer);
        if (str_contains($className, 'Premium')) {
            $customerType = 'premium';
        } elseif (str_contains($className, 'Corporate')) {
            $customerType = 'corporate';
        }

        return [
            'customer_type' => $customerType,
            'credit_score' => rand(50, 95),
            'claims_history' => rand(0, 3),
            'customer_number' => $customer->getCustomerNumber(),
        ];
    }

    private function generateFormulas(
        InsuranceQuoteRequest $request,
        array $riskProfile,
        array $activeCampaigns,
        string $period
    ): array {
        $formulas = [];
        $levels = ['basic', 'standard', 'premium'];

        foreach ($levels as $level) {
            // Calculate base premium with risk adjustments
            $riskResult = $this->riskAdjustmentStrategy->calculateRiskPremium(
                $request->getInsuranceType(),
                $riskProfile['credit_score'],
                $request->getAssetDetails(),
                $riskProfile['customer_type']
            );

            $basePremium = $riskResult['base_premium'];

            // Apply level multiplier
            $levelMultiplier = match ($level) {
                'basic' => 0.7,
                'standard' => 1.0,
                'premium' => 1.5,
                default => 1.0,
            };

            $basePremium *= $levelMultiplier;

            // Apply discounts
            $discountResult = $this->campaignDiscountApplier->applyDiscounts(
                $basePremium,
                $activeCampaigns,
                $request->getInsuranceType(),
                $period
            );

            $annualPremium = $discountResult['final_premium'];
            $monthlyPremium = $annualPremium / 12;

            // Define coverages
            $coverages = $this->getCoveragesForLevel($level, $request->getInsuranceType());
            
            // Define deductible
            $deductible = $this->getDeductibleForLevel($level, $request->getAssetDetails());

            $formulas[] = new InsuranceFormula(
                ucfirst($level),
                $level,
                $annualPremium,
                $monthlyPremium,
                $coverages,
                $deductible,
                $discountResult['discounts']
            );
        }

        return $formulas;
    }

    private function getCoveragesForLevel(string $level, string $insuranceType): array
    {
        $baseCoverages = match ($insuranceType) {
            'HOME' => [
                'Dommages au bâtiment',
                'Responsabilité civile',
                'Vol et vandalisme',
            ],
            'AUTO' => [
                'Responsabilité civile',
                'Dommages collision',
                'Vol',
            ],
            'LIFE' => [
                'Décès toutes causes',
                'Invalidité permanente',
            ],
            default => ['Protection de base'],
        };

        if ($level === 'premium') {
            $baseCoverages[] = 'Protection juridique';
            $baseCoverages[] = 'Assistance 24/7';
            $baseCoverages[] = 'Garantie remplacement à neuf';
        } elseif ($level === 'standard') {
            $baseCoverages[] = 'Protection juridique';
        }

        return $baseCoverages;
    }

    private function getDeductibleForLevel(string $level, array $assetDetails): float
    {
        $baseValue = $assetDetails['value'] ?? 100000;
        
        return match ($level) {
            'basic' => $baseValue * 0.05,
            'standard' => $baseValue * 0.02,
            'premium' => $baseValue * 0.01,
            default => 500.0,
        };
    }

    private function determineRecommendedFormula(array $formulas, array $riskProfile): string
    {
        // Recommend based on customer type
        if ($riskProfile['customer_type'] === 'premium') {
            return 'premium';
        }
        
        if ($riskProfile['credit_score'] >= 75) {
            return 'standard';
        }
        
        return 'basic';
    }
}
