<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Depot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function add(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPriceCartOrder(Order $entity): Integer
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
            SELECT SUM(co.quantity*c.price) s
            FROM App\Entity\CartOrder co
            JOIN co.cart c
            WHERE co.orders = :order
            '
        )->setParameter('order', $entity);

        return $query->getResult();
    }

    public function findTotalPriceOrder(Depot $depot) 
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery (
        '
        SELECT SUM(o.price) price, COUNT(o.id) orders
        FROM App\Entity\Order o
        WHERE o.depot = :depot
        '
        )->setParameter('depot', $depot);
            return $query->getSingleResult();
        }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

}
