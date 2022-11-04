<?php

namespace App\Repository;

use App\Entity\SalesStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesStatus>
 *
 * @method SalesStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalesStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalesStatus[]    findAll()
 * @method SalesStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalesStatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesStatus::class);
    }

    public function add(SalesStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SalesStatus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
