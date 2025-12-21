<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DataSource\LegacyCustomerDataSource;
use App\DataSource\LegacyPolicyDataSource;
use App\DataSource\ExternalRatingDataSource;
use App\DataSource\HistoricalRatesDataSource;
use App\DataSource\ComplianceDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/legacy', name: 'api_legacy_')]
#[OA\Tag(name: 'Legacy Data')]
class LegacyController extends AbstractController
{
    public function __construct(
        private LegacyCustomerDataSource $customerDataSource,
        private LegacyPolicyDataSource $policyDataSource,
        private ExternalRatingDataSource $ratingDataSource,
        private HistoricalRatesDataSource $ratesDataSource,
        private ComplianceDataSource $complianceDataSource
    ) {
    }

    #[Route('/customers/{legacyId}', name: 'customer', methods: ['GET'])]
    #[OA\Get(
        summary: 'Données legacy d\'un client',
        parameters: [
            new OA\Parameter(name: 'legacyId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Données client retournées'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function customer(string $legacyId): JsonResponse
    {
        try {
            $data = $this->customerDataSource->getCustomerData($legacyId);
            return $this->json($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/customers/{legacyId}/history', name: 'customer_history', methods: ['GET'])]
    #[OA\Get(
        summary: 'Historique legacy d\'un client',
        parameters: [
            new OA\Parameter(name: 'legacyId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Historique retourné'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function customerHistory(string $legacyId): JsonResponse
    {
        try {
            $data = $this->customerDataSource->getCustomerData($legacyId);
            return $this->json([
                'customer_id' => $legacyId,
                'history' => $data['history'] ?? []
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/policies/{policyId}', name: 'policy', methods: ['GET'])]
    #[OA\Get(
        summary: 'Données legacy d\'une police',
        parameters: [
            new OA\Parameter(name: 'policyId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Données police retournées'),
            new OA\Response(response: 404, description: 'Police non trouvée')
        ]
    )]
    public function policy(string $policyId): JsonResponse
    {
        try {
            $data = $this->policyDataSource->getPolicyData($policyId);
            return $this->json($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/policies/{policyId}/claims', name: 'policy_claims', methods: ['GET'])]
    #[OA\Get(
        summary: 'Historique des sinistres d\'une police',
        parameters: [
            new OA\Parameter(name: 'policyId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Historique retourné'),
            new OA\Response(response: 404, description: 'Police non trouvée')
        ]
    )]
    public function policyClaims(string $policyId): JsonResponse
    {
        try {
            $data = $this->policyDataSource->getPolicyData($policyId);
            return $this->json([
                'policy_id' => $policyId,
                'claims' => $data['claims'] ?? []
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/ratings/credit/{customerId}', name: 'credit_rating', methods: ['GET'])]
    #[OA\Get(
        summary: 'Credit rating d\'un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rating retourné')
        ]
    )]
    public function creditRating(string $customerId): JsonResponse
    {
        $data = $this->ratingDataSource->getCreditRating($customerId);
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/ratings/market', name: 'market_data', methods: ['GET'])]
    #[OA\Get(
        summary: 'Données marché',
        responses: [
            new OA\Response(response: 200, description: 'Données retournées')
        ]
    )]
    public function marketData(): JsonResponse
    {
        $data = $this->ratingDataSource->getMarketData();
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/rates/interest', name: 'interest_rates', methods: ['GET'])]
    #[OA\Get(
        summary: 'Taux d\'intérêt',
        responses: [
            new OA\Response(response: 200, description: 'Taux retournés')
        ]
    )]
    public function interestRates(): JsonResponse
    {
        $data = $this->ratesDataSource->getInterestRates();
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/rates/exchange', name: 'exchange_rates', methods: ['GET'])]
    #[OA\Get(
        summary: 'Taux de change',
        responses: [
            new OA\Response(response: 200, description: 'Taux retournés')
        ]
    )]
    public function exchangeRates(): JsonResponse
    {
        $data = $this->ratesDataSource->getExchangeRates();
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/compliance/kyc/{customerId}', name: 'kyc', methods: ['GET'])]
    #[OA\Get(
        summary: 'Données KYC d\'un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Données KYC retournées')
        ]
    )]
    public function kyc(string $customerId): JsonResponse
    {
        $data = $this->complianceDataSource->getKycData($customerId);
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/compliance/aml/{customerId}', name: 'aml', methods: ['GET'])]
    #[OA\Get(
        summary: 'Vérifications AML d\'un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Données AML retournées')
        ]
    )]
    public function aml(string $customerId): JsonResponse
    {
        $data = $this->complianceDataSource->getAmlChecks($customerId);
        return $this->json($data, Response::HTTP_OK);
    }
}
