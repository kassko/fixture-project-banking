<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }
    
    /**
     * Find customers by type.
     *
     * @return Customer[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find active customers.
     *
     * @return Customer[]
     */
    public function findActive(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('c.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find customer by customer number.
     */
    public function findByCustomerNumber(string $customerNumber): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.customerNumber = :number')
            ->setParameter('number', $customerNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
