<?php

declare(strict_types=1);

namespace App\Service\Onboarding;

use App\Context\UnifiedContext;
use App\DTO\Request\OnboardingJourneyRequest;
use App\DTO\Response\OnboardingJourneyResponse;
use App\DTO\Response\OnboardingStep;
use App\DTO\Response\WelcomeOffer;
use App\Repository\CustomerRepository;
use App\Tenant\TenantConfigurationLoader;
use App\Brand\BrandConfigurationLoader;
use App\Service\Onboarding\DocumentRequirement\DocumentRequirementResolver;

class OnboardingJourneyService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private TenantConfigurationLoader $tenantConfigLoader,
        private BrandConfigurationLoader $brandConfigLoader,
        private DocumentRequirementResolver $documentResolver
    ) {
    }

    public function buildJourney(OnboardingJourneyRequest $request, UnifiedContext $context): OnboardingJourneyResponse
    {
        $tenantConfig = $this->tenantConfigLoader->load($context->tenant->getTenantId());
        $brandConfig = $this->brandConfigLoader->load($context->brand->getBrandId());

        // 1. Déterminer les étapes selon le type de client et la brand
        $steps = $this->buildSteps($request, $tenantConfig, $brandConfig, $context);

        // 2. Déterminer les documents requis
        $requiredDocuments = $this->documentResolver->resolve(
            customerType: $request->customerType,
            targetProduct: $request->targetProduct,
            country: $tenantConfig->getCountry(),
            existingDocuments: $request->existingDocuments
        );

        // 3. Déterminer les offres de bienvenue
        $welcomeOffers = $this->getWelcomeOffers($request, $brandConfig, $context);

        // 4. Calculer le temps estimé
        $estimatedTime = $this->calculateEstimatedTime($steps, $request->channel);

        // 5. Assigner un conseiller si brand premium
        $advisor = $brandConfig->getSegment() === 'premium' 
            ? $this->assignDedicatedAdvisor($tenantConfig) 
            : null;

        return new OnboardingJourneyResponse(
            journeyId: uniqid('JRN-'),
            steps: $steps,
            requiredDocuments: $requiredDocuments,
            welcomeOffers: $welcomeOffers,
            estimatedCompletionTime: $estimatedTime,
            dedicatedAdvisor: $advisor
        );
    }

    private function buildSteps($request, $tenantConfig, $brandConfig, UnifiedContext $context): array
    {
        $steps = [];
        $order = 1;

        // Étape 1: Création de compte (toujours)
        $steps[] = new OnboardingStep(
            order: $order++,
            code: 'ACCOUNT_CREATION',
            name: 'Création du compte',
            status: 'PENDING',
            required: true,
            estimatedMinutes: 5
        );

        // Étape 2: Vérification d'identité
        $steps[] = new OnboardingStep(
            order: $order++,
            code: 'IDENTITY_VERIFICATION',
            name: 'Vérification d\'identité',
            status: 'PENDING',
            required: true,
            estimatedMinutes: $request->channel === 'AGENCY' ? 5 : 10,
            config: ['provider' => $tenantConfig->getKycProvider()]
        );

        // Étape 3: Documents (si entreprise, plus de documents)
        if ($request->customerType === 'CORPORATE') {
            $steps[] = new OnboardingStep(
                order: $order++,
                code: 'COMPANY_DOCUMENTS',
                name: 'Documents entreprise',
                status: 'PENDING',
                required: true,
                estimatedMinutes: 15
            );
        }

        // Étape 4: Justificatif de revenus (si prêt ou produit premium)
        if ($this->requiresIncomeProof($request->targetProduct, $brandConfig)) {
            $steps[] = new OnboardingStep(
                order: $order++,
                code: 'INCOME_VERIFICATION',
                name: 'Justificatif de revenus',
                status: 'PENDING',
                required: true,
                estimatedMinutes: 5
            );
        }

        // Étape 5: Signature électronique
        $steps[] = new OnboardingStep(
            order: $order++,
            code: 'SIGNATURE',
            name: 'Signature électronique',
            status: 'PENDING',
            required: true,
            estimatedMinutes: 3
        );

        // Étape 6: Offre de bienvenue (si campagne active)
        if ($context->campaign && $context->campaign->getId()) {
            $steps[] = new OnboardingStep(
                order: $order++,
                code: 'WELCOME_OFFER',
                name: 'Offre de bienvenue',
                status: 'PENDING',
                required: false,
                estimatedMinutes: 2,
                config: ['campaign_id' => $context->campaign->getId()]
            );
        }

        // Étape 7: Rendez-vous conseiller (si premium)
        if ($brandConfig->getSegment() === 'premium') {
            $steps[] = new OnboardingStep(
                order: $order++,
                code: 'ADVISOR_MEETING',
                name: 'Rendez-vous avec votre conseiller',
                status: 'PENDING',
                required: false,
                estimatedMinutes: 30
            );
        }

        return $steps;
    }

    private function getWelcomeOffers($request, $brandConfig, UnifiedContext $context): array
    {
        $offers = [];

        // Offre standard de la brand
        if ($brandOffer = $brandConfig->getWelcomeOffer()) {
            $offers[] = new WelcomeOffer(
                code: $brandOffer['code'],
                name: $brandOffer['name'],
                description: $brandOffer['description'],
                value: $brandOffer['value'],
                validDays: $brandOffer['valid_days']
            );
        }

        // Offre campagne
        if ($context->campaign && $context->campaign->getId() && ($campaignOffer = $context->campaign->getWelcomeOffer())) {
            $offers[] = new WelcomeOffer(
                code: $campaignOffer['code'],
                name: $campaignOffer['name'],
                description: $campaignOffer['description'],
                value: $campaignOffer['value'],
                validDays: $campaignOffer['valid_days']
            );
        }

        // Offre saisonnière
        if ($context->temporal->isPromotionalPeriod()) {
            $seasonalOffer = $context->temporal->getCurrentPromotion();
            if ($seasonalOffer) {
                $offers[] = new WelcomeOffer(
                    code: $seasonalOffer['code'],
                    name: $seasonalOffer['name'],
                    description: $seasonalOffer['description'],
                    value: $seasonalOffer['value'],
                    validDays: $seasonalOffer['valid_days']
                );
            }
        }

        return $offers;
    }

    private function requiresIncomeProof(string $product, $brandConfig): bool
    {
        $productsRequiringIncome = ['LOAN_PERSONAL', 'LOAN_HOME', 'PREMIUM_ACCOUNT'];
        return in_array($product, $productsRequiringIncome) 
            || $brandConfig->getSegment() === 'premium';
    }

    private function calculateEstimatedTime(array $steps, string $channel): int
    {
        $total = array_sum(array_map(fn($s) => $s->estimatedMinutes, $steps));
        
        // Canal agence = plus rapide (aide du conseiller)
        if ($channel === 'AGENCY') {
            $total = (int)($total * 0.7);
        }
        
        return $total;
    }

    private function assignDedicatedAdvisor($tenantConfig): array
    {
        // Simulation d'assignation d'un conseiller
        return [
            'id' => 'ADV-' . rand(100, 999),
            'name' => 'Marie Dupont',
            'email' => 'marie.dupont@' . strtolower($tenantConfig->get('name', 'banque')) . '.fr',
            'phone' => '+33 1 23 45 67 89',
        ];
    }
}
