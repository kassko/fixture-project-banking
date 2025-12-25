<?php

namespace App\Controller\Api;

use App\Service\CustomerService;
use App\Service\RiskAssessmentService;
use App\Context\UserContext;
use App\Context\FeatureFlagContext;
use App\FeatureFlag\FeatureFlagService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/customers')]
class CustomerController
{
    private CustomerService $customerService;
    private RiskAssessmentService $riskService;
    private FeatureFlagService $featureFlagService;

    public function __construct(
        CustomerService $customerService,
        RiskAssessmentService $riskService,
        FeatureFlagService $featureFlagService
    ) {
        $this->customerService = $customerService;
        $this->riskService = $riskService;
        $this->featureFlagService = $featureFlagService;
    }

    /**
     * GET /api/customers/{id} - Get a customer
     */
    #[Route('/{id}', name: 'api_customer_get', methods: ['GET'])]
    public function getCustomer(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        $customer = $this->customerService->getCustomer($id, $userContext, $featureFlagContext);

        if ($customer === null) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        return new JsonResponse($customer->toArray());
    }

    /**
     * GET /api/customers/{id}/full - Get customer with all data from multiple sources
     */
    #[Route('/{id}/full', name: 'api_customer_get_full', methods: ['GET'])]
    public function getCustomerFull(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        $allData = $this->customerService->getCustomerFull($id, $userContext, $featureFlagContext);

        if (empty($allData)) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        return new JsonResponse([
            'customerId' => $id,
            'sources' => $allData,
        ]);
    }

    /**
     * GET /api/customers/{id}/profile - Get customer profile according to permissions
     */
    #[Route('/{id}/profile', name: 'api_customer_profile', methods: ['GET'])]
    public function getCustomerProfile(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        $profile = $this->customerService->getCustomerProfile($id, $userContext, $featureFlagContext);

        if ($profile === null) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        return new JsonResponse([
            'customerId' => $id,
            'profile' => $profile,
            'viewedAs' => $userContext->getRole(),
        ]);
    }

    /**
     * POST /api/customers/search - Search customers with dynamic source selection
     */
    #[Route('/search', name: 'api_customer_search', methods: ['POST'])]
    public function searchCustomers(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        // Simulate search by getting multiple customers
        $customerId = $data['id'] ?? 1;
        $customer = $this->customerService->getCustomer($customerId, $userContext, $featureFlagContext);

        $results = $customer !== null ? [$customer->toArray()] : [];

        return new JsonResponse([
            'results' => $results,
            'total' => count($results),
        ]);
    }

    /**
     * GET /api/customers/{id}/risk-summary - Get risk summary for customer
     */
    #[Route('/{id}/risk-summary', name: 'api_customer_risk_summary', methods: ['GET'])]
    public function getRiskSummary(int $id, Request $request): JsonResponse
    {
        $userContext = $this->createUserContext($request);
        $featureFlagContext = $this->featureFlagService->createContext($userContext);

        // Get customer to determine type
        $customer = $this->customerService->getCustomer($id, $userContext, $featureFlagContext);
        
        if ($customer === null) {
            return new JsonResponse(['error' => 'Customer not found'], 404);
        }

        $customerType = $customer->getCustomerType();

        // Get risk factors with masking
        $factors = $this->riskService->getFactors($id, $userContext, $featureFlagContext);

        return new JsonResponse([
            'customerId' => $id,
            'customerType' => $customerType,
            'riskData' => $factors,
        ]);
    }

    private function createUserContext(Request $request): UserContext
    {
        // In real app, would get from authentication
        $role = $request->query->get('role', 'user');
        $userId = (int) $request->query->get('userId', 1);

        $permissions = match($role) {
            'admin' => ['view_all', 'view_pii', 'view_risk'],
            'manager' => ['view_all', 'view_risk'],
            default => ['view_basic'],
        };

        return new UserContext($userId, $role, $permissions);
    }
}
