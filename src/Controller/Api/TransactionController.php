<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/transactions', name: 'api_transactions_')]
#[OA\Tag(name: 'Transactions')]
class TransactionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des transactions',
        responses: [
            new OA\Response(response: 200, description: 'Liste des transactions retournée')
        ]
    )]
    public function list(): JsonResponse
    {
        $transactions = $this->entityManager->getRepository(Transaction::class)->findAll();
        
        return $this->json($transactions, Response::HTTP_OK, [], [
            'groups' => ['transaction:read']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail d\'une transaction',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaction trouvée'),
            new OA\Response(response: 404, description: 'Transaction non trouvée')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $transaction = $this->entityManager->getRepository(Transaction::class)->find($id);
        
        if (!$transaction) {
            return $this->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($transaction, Response::HTTP_OK, [], [
            'groups' => ['transaction:read']
        ]);
    }

    #[Route('/reference/{reference}', name: 'by_reference', methods: ['GET'])]
    #[OA\Get(
        summary: 'Recherche par référence',
        parameters: [
            new OA\Parameter(name: 'reference', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Transaction trouvée'),
            new OA\Response(response: 404, description: 'Transaction non trouvée')
        ]
    )]
    public function byReference(string $reference): JsonResponse
    {
        $transaction = $this->entityManager->getRepository(Transaction::class)->findOneBy(['reference' => $reference]);
        
        if (!$transaction) {
            return $this->json(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($transaction, Response::HTTP_OK, [], [
            'groups' => ['transaction:read']
        ]);
    }
}
