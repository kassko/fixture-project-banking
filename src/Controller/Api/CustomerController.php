<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\Customer\IndividualCustomer;
use App\Entity\Customer\CorporateCustomer;
use App\Entity\Customer\PremiumCustomer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/customers', name: 'api_customers_')]
#[OA\Tag(name: 'Customers')]
class CustomerController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste tous les clients',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des clients retournée avec succès'
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $customers = $this->entityManager->getRepository(Customer::class)->findAll();
        
        return $this->json($customers, Response::HTTP_OK, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail d\'un client',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Client trouvé'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($customer, Response::HTTP_OK, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/individual', name: 'create_individual', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un client individuel',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'customerNumber', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function createIndividual(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $customer = new IndividualCustomer();
        $customer->setFirstName($data['firstName'] ?? '');
        $customer->setLastName($data['lastName'] ?? '');
        $customer->setCustomerNumber($data['customerNumber'] ?? 'CUST-' . uniqid());
        
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        
        return $this->json($customer, Response::HTTP_CREATED, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/corporate', name: 'create_corporate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un client entreprise',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'companyName', type: 'string'),
                    new OA\Property(property: 'customerNumber', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function createCorporate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $customer = new CorporateCustomer();
        $customer->setCompanyName($data['companyName'] ?? '');
        $customer->setCustomerNumber($data['customerNumber'] ?? 'CORP-' . uniqid());
        $customer->setFirstName(''); // Required by parent class
        $customer->setLastName(''); // Required by parent class
        
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        
        return $this->json($customer, Response::HTTP_CREATED, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/premium', name: 'create_premium', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un client premium',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'customerNumber', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function createPremium(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $customer = new PremiumCustomer();
        $customer->setFirstName($data['firstName'] ?? '');
        $customer->setLastName($data['lastName'] ?? '');
        $customer->setCustomerNumber($data['customerNumber'] ?? 'PREM-' . uniqid());
        
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
        
        return $this->json($customer, Response::HTTP_CREATED, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Modifier un client',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Client modifié'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if (isset($data['firstName']) && method_exists($customer, 'setFirstName')) {
            $customer->setFirstName($data['firstName']);
        }
        if (isset($data['lastName']) && method_exists($customer, 'setLastName')) {
            $customer->setLastName($data['lastName']);
        }
        
        $this->entityManager->flush();
        
        return $this->json($customer, Response::HTTP_OK, [], [
            'groups' => ['customer:read']
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Supprimer un client',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Client supprimé'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $customer = $this->entityManager->getRepository(Customer::class)->find($id);
        
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }
        
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
        
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
