<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\BankAccount;
use App\Entity\Transaction;
use App\Enum\TransactionType;
use App\Model\Financial\MoneyAmount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/accounts', name: 'api_accounts_')]
#[OA\Tag(name: 'Bank Accounts')]
class BankAccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des comptes bancaires',
        responses: [
            new OA\Response(response: 200, description: 'Liste des comptes retournée')
        ]
    )]
    public function list(): JsonResponse
    {
        $accounts = $this->entityManager->getRepository(BankAccount::class)->findAll();
        
        return $this->json($accounts, Response::HTTP_OK, [], [
            'groups' => ['account:read']
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail d\'un compte',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Compte trouvé'),
            new OA\Response(response: 404, description: 'Compte non trouvé')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $account = $this->entityManager->getRepository(BankAccount::class)->find($id);
        
        if (!$account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($account, Response::HTTP_OK, [], [
            'groups' => ['account:read']
        ]);
    }

    #[Route('/{id}/deposit', name: 'deposit', methods: ['POST'])]
    #[OA\Post(
        summary: 'Effectuer un dépôt',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'amount', type: 'number'),
                    new OA\Property(property: 'currency', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Dépôt effectué'),
            new OA\Response(response: 404, description: 'Compte non trouvé')
        ]
    )]
    public function deposit(int $id, Request $request): JsonResponse
    {
        $account = $this->entityManager->getRepository(BankAccount::class)->find($id);
        
        if (!$account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        $amount = new MoneyAmount((float)($data['amount'] ?? 0), $data['currency'] ?? 'EUR');
        
        $account->deposit($amount);
        
        // Create transaction record
        $transaction = new Transaction();
        $transaction->setTransactionId('DEP-' . uniqid());
        $transaction->setType(TransactionType::DEPOSIT);
        $transaction->setAmount($amount);
        $transaction->setAccount($account);
        $transaction->setStatus('completed');
        $transaction->setDescription('Deposit');
        
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
        
        return $this->json([
            'account' => $account,
            'transaction' => $transaction
        ], Response::HTTP_OK, [], [
            'groups' => ['account:read', 'transaction:read']
        ]);
    }

    #[Route('/{id}/withdraw', name: 'withdraw', methods: ['POST'])]
    #[OA\Post(
        summary: 'Effectuer un retrait',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'amount', type: 'number'),
                    new OA\Property(property: 'currency', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Retrait effectué'),
            new OA\Response(response: 400, description: 'Solde insuffisant'),
            new OA\Response(response: 404, description: 'Compte non trouvé')
        ]
    )]
    public function withdraw(int $id, Request $request): JsonResponse
    {
        $account = $this->entityManager->getRepository(BankAccount::class)->find($id);
        
        if (!$account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        $amount = new MoneyAmount((float)($data['amount'] ?? 0), $data['currency'] ?? 'EUR');
        
        try {
            $account->withdraw($amount);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        
        // Create transaction record
        $transaction = new Transaction();
        $transaction->setTransactionId('WTH-' . uniqid());
        $transaction->setType(TransactionType::WITHDRAWAL);
        $transaction->setAmount($amount);
        $transaction->setAccount($account);
        $transaction->setStatus('completed');
        $transaction->setDescription('Withdrawal');
        
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
        
        return $this->json([
            'account' => $account,
            'transaction' => $transaction
        ], Response::HTTP_OK, [], [
            'groups' => ['account:read', 'transaction:read']
        ]);
    }

    #[Route('/{id}/transfer', name: 'transfer', methods: ['POST'])]
    #[OA\Post(
        summary: 'Effectuer un virement',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'target_account_id', type: 'integer'),
                    new OA\Property(property: 'amount', type: 'number'),
                    new OA\Property(property: 'currency', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Virement effectué'),
            new OA\Response(response: 400, description: 'Erreur'),
            new OA\Response(response: 404, description: 'Compte non trouvé')
        ]
    )]
    public function transfer(int $id, Request $request): JsonResponse
    {
        $account = $this->entityManager->getRepository(BankAccount::class)->find($id);
        
        if (!$account) {
            return $this->json(['error' => 'Source account not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = json_decode($request->getContent(), true);
        $targetAccount = $this->entityManager->getRepository(BankAccount::class)->find($data['target_account_id'] ?? 0);
        
        if (!$targetAccount) {
            return $this->json(['error' => 'Target account not found'], Response::HTTP_NOT_FOUND);
        }
        
        $amount = new MoneyAmount((float)($data['amount'] ?? 0), $data['currency'] ?? 'EUR');
        
        try {
            $account->withdraw($amount);
            $targetAccount->deposit($amount);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        
        // Create transaction records
        $transactionOut = new Transaction();
        $transactionOut->setTransactionId('TRF-OUT-' . uniqid());
        $transactionOut->setType(TransactionType::TRANSFER);
        $transactionOut->setAmount($amount);
        $transactionOut->setAccount($account);
        $transactionOut->setStatus('completed');
        $transactionOut->setDescription('Transfer to account ' . $targetAccount->getAccountNumber());
        
        $transactionIn = new Transaction();
        $transactionIn->setTransactionId('TRF-IN-' . uniqid());
        $transactionIn->setType(TransactionType::TRANSFER);
        $transactionIn->setAmount($amount);
        $transactionIn->setAccount($targetAccount);
        $transactionIn->setStatus('completed');
        $transactionIn->setDescription('Transfer from account ' . $account->getAccountNumber());
        
        $this->entityManager->persist($transactionOut);
        $this->entityManager->persist($transactionIn);
        $this->entityManager->flush();
        
        return $this->json([
            'source_account' => $account,
            'target_account' => $targetAccount,
            'transactions' => [$transactionOut, $transactionIn]
        ], Response::HTTP_OK, [], [
            'groups' => ['account:read', 'transaction:read']
        ]);
    }

    #[Route('/{id}/transactions', name: 'transactions', methods: ['GET'])]
    #[OA\Get(
        summary: 'Historique des transactions',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Historique retourné'),
            new OA\Response(response: 404, description: 'Compte non trouvé')
        ]
    )]
    public function transactions(int $id): JsonResponse
    {
        $account = $this->entityManager->getRepository(BankAccount::class)->find($id);
        
        if (!$account) {
            return $this->json(['error' => 'Account not found'], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($account->getTransactions()->toArray(), Response::HTTP_OK, [], [
            'groups' => ['transaction:read']
        ]);
    }
}
