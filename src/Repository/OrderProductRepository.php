<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderProduct>
 *
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    public function add(OrderProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrderProduct $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDate(string $startDate = null, string $endDate = null): array
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        $query->select('op')
              ->from('App\Entity\OrderProduct', 'op')
              ->innerJoin('op.product', 'p')
              ->orderBy('p.name', 'ASC');
        
        if ($startDate !== null && $endDate === null) {
            $query->innerJoin('op.orders', 'o')
                  ->where('o.orderedAt > :startDate')
                  ->setParameters(['startDate' => $startDate]);
        }

        if ($startDate === null && $endDate !== null) {
            $query->innerJoin('op.orders', 'o')
                  ->where('o.orderedAt < :endDate')
                  ->setParameters(['endDate' => $endDate]);
        }

        if ($startDate !== null && $endDate !== null) {
            $query->innerJoin('op.orders', 'o')
                  ->where('o.orderedAt BETWEEN :startDate AND :endDate')
                  ->setParameters(['startDate' => $startDate, 'endDate' => $endDate]);
        }

        return $query->getQuery()->getResult();

    }

    public function getTotalQuantityByProducts()
    {
        $em = $this->getEntityManager();
        
        $query = $em->createQueryBuilder();
        $query->select(['p'])
              ->from('App\Entity\Product', 'p')
              ->groupBy('p.name');

        return $query->getQuery()->getResult();
    }
}
