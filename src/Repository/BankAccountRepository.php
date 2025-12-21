<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BankAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BankAccount>
 */
class BankAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankAccount::class);
    }
    
    /**
     * Find accounts by customer ID.
     *
     * @return BankAccount[]
     */
    public function findByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.customer = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('a.accountNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find active accounts.
     *
     * @return BankAccount[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find account by account number.
     */
    public function findByAccountNumber(string $accountNumber): ?BankAccount
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.accountNumber = :number')
            ->setParameter('number', $accountNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    /**
     * Find accounts by type.
     *
     * @return BankAccount[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}
