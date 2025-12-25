<?php

namespace App\Controller\Api;

use App\Service\RiskAssessmentService;
use App\Context\UserContext;
use App\FeatureFlag\FeatureFlagService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/risk')]
class RiskController
{
    private RiskAssessmentService $riskService;
    private FeatureFlagService $featureFlagService;

    public function __construct(
        RiskAssessmentService $riskService,
        FeatureFlagService $featureFlagService
    ) {
        $this->riskService = $riskService;
        $this->featureFlagService = $featureFlagService;
    }

    /**
     * GET /api/risk/assess/{customerId} - Complex risk assessment with composition
     * Use Case 1: Runtime source resolution based on customer type
     */
    #[Route('/assess/{customerId}', name: 'api_risk_assess', methods: ['GET'])]
    public function assess(int $customerId, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $customerType = $request->query->get('customerType', 'standard');

        $assessment = $this->riskService->assess($customerId, $customerType, $userContext);

        if ($assessment === null) {
            return new JsonResponse(['error' => 'Risk assessment not available'], 404);
        }

        return new JsonResponse([
            'customerId' => $customerId,
            'customerType' => $customerType,
            'assessment' => $assessment->toArray(),
        ]);
    }

    /**
     * GET /api/risk/score/{customerId} - Get risk score with fallback chain
     * Use Case 2: Fallback cascade (CreditBureau → Cache → Legacy → Default)
     */
    #[Route('/score/{customerId}', name: 'api_risk_score', methods: ['GET'])]
    public function getScore(int $customerId, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        
        $score = $this->riskService->getScore($customerId, $userContext);

        if ($score === null) {
            return new JsonResponse(['error' => 'Risk score not available'], 404);
        }

        return new JsonResponse([
            'customerId' => $customerId,
            'score' => $score,
            'note' => 'Retrieved using fallback chain if primary sources unavailable',
        ]);
    }

    /**
     * POST /api/risk/simulate - Simulate risk scenarios with runtime resolution
     */
    #[Route('/simulate', name: 'api_risk_simulate', methods: ['POST'])]
    public function simulate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $customerId = $data['customerId'] ?? 1;
        $scenarios = $data['scenarios'] ?? [
            ['name' => 'standard', 'customerType' => 'standard'],
            ['name' => 'premium', 'customerType' => 'premium'],
            ['name' => 'corporate', 'customerType' => 'corporate'],
        ];

        $userContext = $this->createUserContext($request);
        
        $results = $this->riskService->simulate($scenarios, $customerId, $userContext);

        return new JsonResponse([
            'customerId' => $customerId,
            'scenarios' => $results,
            'scenarioCount' => count($results),
        ]);
    }

    /**
     * GET /api/risk/report/{customerId} - Comprehensive report with multi-source concurrency
     * Use Case 4: Conflict resolution when multiple sources return different data
     */
    #[Route('/report/{customerId}', name: 'api_risk_report', methods: ['GET'])]
    public function getReport(int $customerId, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $customerType = $request->query->get('customerType', 'standard');
        $conflictStrategy = $request->query->get('strategy', 'conservative');

        $report = $this->riskService->getReport(
            $customerId,
            $customerType,
            $userContext,
            $conflictStrategy
        );

        if ($report === null) {
            return new JsonResponse(['error' => 'Risk report not available'], 404);
        }

        return new JsonResponse([
            'report' => $report,
            'note' => 'Data resolved from multiple sources using ' . $conflictStrategy . ' strategy',
        ]);
    }

    /**
     * GET /api/risk/factors/{customerId} - Get risk factors with masking
     * Use Case 3: Data masking based on user role and feature flags
     * - Admin → all data
     * - Manager → data without PII
     * - User → aggregated data only
     */
    #[Route('/factors/{customerId}', name: 'api_risk_factors', methods: ['GET'])]
    public function getFactors(int $customerId, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        $factors = $this->riskService->getFactors($customerId, $userContext, $featureFlagContext);

        if ($factors === null) {
            return new JsonResponse(['error' => 'Risk factors not available'], 404);
        }

        return new JsonResponse([
            'customerId' => $customerId,
            'factors' => $factors,
            'viewedAs' => $userContext->getRole(),
            'masking' => [
                'show_detailed_risk' => $featureFlagContext->isEnabled('show_detailed_risk'),
                'show_credit_score' => $featureFlagContext->isEnabled('show_credit_score'),
            ],
        ]);
    }

    private function createUserContext(Request $request): UserContext
    {
        // In real app, would get from authentication
        $role = $request->query->get('role', 'user');
        $userId = (int) $request->query->get('userId', 1);

        $permissions = match($role) {
            'admin' => ['view_all', 'view_pii', 'view_risk', 'view_sensitive'],
            'manager' => ['view_all', 'view_risk'],
            default => ['view_basic'],
        };

        return new UserContext($userId, $role, $permissions);
    }
}
