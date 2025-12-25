<?php

namespace App\Service;

use App\Model\Risk\RiskAssessment;
use App\Model\Risk\RiskProfile;
use App\Model\Risk\RiskScore;
use App\Hydrator\RiskHydrator;
use App\Resolver\SourceResolver;
use App\Resolver\FallbackResolver;
use App\Resolver\ConflictResolver;
use App\Resolver\DataMaskingResolver;
use App\Context\UserContext;
use App\Context\FeatureFlagContext;

class RiskAssessmentService
{
    private RiskHydrator $hydrator;
    private SourceResolver $sourceResolver;
    private FallbackResolver $fallbackResolver;
    private ConflictResolver $conflictResolver;
    private DataMaskingResolver $maskingResolver;

    public function __construct(
        RiskHydrator $hydrator,
        SourceResolver $sourceResolver,
        FallbackResolver $fallbackResolver,
        ConflictResolver $conflictResolver,
        DataMaskingResolver $maskingResolver
    ) {
        $this->hydrator = $hydrator;
        $this->sourceResolver = $sourceResolver;
        $this->fallbackResolver = $fallbackResolver;
        $this->conflictResolver = $conflictResolver;
        $this->maskingResolver = $maskingResolver;
    }

    /**
     * Complex risk assessment with composition from multiple sources
     */
    public function assess(
        int $customerId,
        string $customerType = 'standard',
        ?UserContext $userContext = null
    ): ?RiskAssessment {
        // Resolve sources based on customer type (Use Case 1)
        $sources = $this->sourceResolver->resolve('risk', $userContext, [
            'customerType' => $customerType
        ]);

        if (empty($sources)) {
            return null;
        }

        // Collect data from all sources
        $allData = [];
        foreach ($sources as $source) {
            $data = $source->fetchData('risk', $customerId);
            if ($data !== null) {
                $allData[$source->getName()] = $data;
            }
        }

        if (empty($allData)) {
            return null;
        }

        // Resolve conflicts using conservative strategy (most cautious)
        $resolvedData = $this->conflictResolver->resolve($allData, 'conservative');

        // Create risk assessment with composition
        $assessment = new RiskAssessment();
        $assessment->setCustomerId($customerId);

        // Create risk profile from resolved data
        $profile = $this->hydrator->hydrate($resolvedData, new RiskProfile());
        $assessment->setRiskProfile($profile);

        // Extract scores if present
        if (isset($resolvedData['scores']) && is_array($resolvedData['scores'])) {
            foreach ($resolvedData['scores'] as $scoreData) {
                $score = $this->hydrator->hydrate($scoreData, new RiskScore());
                $assessment->addScore($score);
            }
        }

        // Generate recommendation based on risk level
        $riskLevel = $profile->getRiskLevel() ?? 'unknown';
        $assessment->setRecommendation($this->generateRecommendation($riskLevel));
        $assessment->setApproved($this->shouldApprove($riskLevel));

        return $assessment;
    }

    /**
     * Get risk score with fallback chain (Use Case 2)
     */
    public function getScore(
        int $customerId,
        ?UserContext $userContext = null
    ): ?array {
        // Build fallback chain: CreditBureau → Cache → Legacy → Default
        $sources = $this->sourceResolver->resolve('risk', $userContext);
        
        // Setup fallback chain
        $this->fallbackResolver->setupFallbackChain($sources);

        // Fetch with automatic fallback
        $data = $this->fallbackResolver->fetchWithFallback($sources, 'risk', $customerId);

        return $data;
    }

    /**
     * Simulate risk scenarios with runtime resolution
     */
    public function simulate(
        array $scenarios,
        int $customerId,
        ?UserContext $userContext = null
    ): array {
        $results = [];

        foreach ($scenarios as $scenario) {
            $customerType = $scenario['customerType'] ?? 'standard';
            
            // Runtime source resolution based on scenario
            $sources = $this->sourceResolver->resolve('risk', $userContext, [
                'customerType' => $customerType
            ]);

            $data = $this->fallbackResolver->fetchWithFallback($sources, 'risk', $customerId);
            
            if ($data !== null) {
                $results[$scenario['name']] = $data;
            }
        }

        return $results;
    }

    /**
     * Generate comprehensive report with multiple source concurrency (Use Case 4)
     */
    public function getReport(
        int $customerId,
        string $customerType = 'standard',
        ?UserContext $userContext = null,
        string $conflictStrategy = 'conservative'
    ): ?array {
        // Get data from multiple sources concurrently
        $sources = $this->sourceResolver->resolve('risk', $userContext, [
            'customerType' => $customerType
        ]);

        $allSourceData = [];
        foreach ($sources as $source) {
            $data = $source->fetchData('risk', $customerId);
            if ($data !== null) {
                $allSourceData[$source->getName()] = $data;
            }
        }

        if (empty($allSourceData)) {
            return null;
        }

        // Resolve conflicts between sources
        $resolvedData = $this->conflictResolver->resolve($allSourceData, $conflictStrategy);

        return [
            'customerId' => $customerId,
            'data' => $resolvedData,
            'sources' => array_keys($allSourceData),
            'strategy' => $conflictStrategy,
        ];
    }

    /**
     * Get risk factors with masking (Use Case 3)
     */
    public function getFactors(
        int $customerId,
        UserContext $userContext,
        FeatureFlagContext $featureFlagContext
    ): ?array {
        $sources = $this->sourceResolver->resolve('risk', $userContext);
        $data = $this->fallbackResolver->fetchWithFallback($sources, 'risk', $customerId);

        if ($data === null) {
            return null;
        }

        // Apply masking based on user role and feature flags
        $maskedData = $this->maskingResolver->maskData(
            $data,
            $userContext,
            $featureFlagContext,
            'risk'
        );

        return $maskedData;
    }

    private function generateRecommendation(string $riskLevel): string
    {
        return match (strtolower($riskLevel)) {
            'low' => 'Approved for all products',
            'medium-low', 'medium' => 'Approved with standard terms',
            'medium-high' => 'Approved with increased monitoring',
            'high' => 'Requires manual review',
            'critical' => 'Declined - high risk',
            default => 'Requires assessment',
        };
    }

    private function shouldApprove(string $riskLevel): bool
    {
        return in_array(strtolower($riskLevel), ['low', 'medium-low', 'medium']);
    }
}
