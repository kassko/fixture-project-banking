<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }
    
    /**
     * Find transactions by account ID.
     *
     * @return Transaction[]
     */
    public function findByAccount(int $accountId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.account = :accountId')
            ->setParameter('accountId', $accountId)
            ->orderBy('t.transactionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find transactions by status.
     *
     * @return Transaction[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.transactionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find transactions by type.
     *
     * @return Transaction[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :type')
            ->setParameter('type', $type)
            ->orderBy('t.transactionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find recent transactions.
     *
     * @return Transaction[]
     */
    public function findRecent(int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.transactionDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
