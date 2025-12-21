<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\InsurancePolicy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for InsurancePolicy entities.
 *
 * @extends ServiceEntityRepository<InsurancePolicy>
 */
class InsurancePolicyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InsurancePolicy::class);
    }
    
    /**
     * Find active policies for a customer.
     */
    public function findActiveByCustomer(int $customerId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.customer = :customerId')
            ->andWhere('p.status = :status')
            ->setParameter('customerId', $customerId)
            ->setParameter('status', 'ACTIVE')
            ->orderBy('p.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find policies expiring within a given number of days.
     */
    public function findExpiringWithinDays(int $days): array
    {
        $targetDate = new \DateTimeImmutable("+{$days} days");
        
        return $this->createQueryBuilder('p')
            ->where('p.endDate <= :targetDate')
            ->andWhere('p.endDate >= :now')
            ->andWhere('p.status = :status')
            ->setParameter('targetDate', $targetDate)
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('status', 'ACTIVE')
            ->orderBy('p.endDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
