<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\InsurancePolicy;
use App\Entity\Customer;
use App\Enum\PolicyStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/policies', name: 'api_policies_')]
#[OA\Tag(name: 'Insurance Policies')]
class InsurancePolicyController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des polices d\'assurance',
        responses: [
            new OA\Response(response: 200, description: 'Liste des polices retournée')
        ]
    )]
    public function list(): JsonResponse
    {
        $policies = $this->entityManager->getRepository(InsurancePolicy::class)->findAll();
        
        return $this->json($policies, Response::HTTP_OK, [], [
            'groups' => ['policy:read']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail d\'une police',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Police trouvée'),
            new OA\Response(response: 404, description: 'Police non trouvée')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $policy = $this->entityManager->getRepository(InsurancePolicy::class)->find($id);
        
        if (!$policy) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($policy, Response::HTTP_OK, [], [
            'groups' => ['policy:read']
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une police',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'policyNumber', type: 'string'),
                    new OA\Property(property: 'customerId', type: 'integer'),
                    new OA\Property(property: 'premium', type: 'number')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Police créée'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $customer = $this->entityManager->getRepository(Customer::class)->find($data['customerId'] ?? 0);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        $policy = new InsurancePolicy();
        $policy->setPolicyNumber($data['policyNumber'] ?? 'POL-' . uniqid());
        $policy->setCustomer($customer);
        $policy->setPremium((string)($data['premium'] ?? 0));
        $policy->setStatus(PolicyStatus::ACTIVE);
        
        $this->entityManager->persist($policy);
        $this->entityManager->flush();
        
        return $this->json($policy, Response::HTTP_CREATED, [], [
            'groups' => ['policy:read']
        ]);
    }

    #[Route('/{id}/coverages', name: 'coverages', methods: ['GET'])]
    #[OA\Get(
        summary: 'Couvertures d\'une police',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Couvertures retournées'),
            new OA\Response(response: 404, description: 'Police non trouvée')
        ]
    )]
    public function coverages(int $id): JsonResponse
    {
        $policy = $this->entityManager->getRepository(InsurancePolicy::class)->find($id);
        
        if (!$policy) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($policy->getCoverages(), Response::HTTP_OK);
    }

    #[Route('/{id}/beneficiaries', name: 'beneficiaries', methods: ['GET'])]
    #[OA\Get(
        summary: 'Bénéficiaires d\'une police',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Bénéficiaires retournés'),
            new OA\Response(response: 404, description: 'Police non trouvée')
        ]
    )]
    public function beneficiaries(int $id): JsonResponse
    {
        $policy = $this->entityManager->getRepository(InsurancePolicy::class)->find($id);
        
        if (!$policy) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($policy->getBeneficiaries()->toArray(), Response::HTTP_OK, [], [
            'groups' => ['beneficiary:read']
        ]);
    }
}
