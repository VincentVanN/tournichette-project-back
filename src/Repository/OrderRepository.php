<?php

namespace App\Repository;

use App\Entity\Order;
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

    public function getOrdersByDateStart(string $startDate)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
            SELECT o
            FROM App\Entity\Order o
            WHERE o.orderedAt > :startDate
            ORDER BY o.orderedAt
            '
        )->setParameter('startDate', $startDate);

        return $query->getResult();
    }

    public function getOrdersByDateInterval(string $startDate, string $endDate = null, string $orderBy = 'orderedAt', string $sort = 'ASC')
    {
        $entityManager = $this->getEntityManager();

        $sort = $sort !== 'ASC' && $sort !== 'DESC' ? 'ASC' : $sort;

        // $query = $entityManager->createQuery(
        //     '
        //     SELECT o
        //     FROM App\Entity\Order o
        //     JOIN App\Entity\User u
        //     WHERE o.orderedAt BETWEEN :startDate AND :endDate
        //     ORDER BY u.' . $orderBy . '
        //     '
        // )->setParameters(['startDate' => $startDate, 'endDate' => $endDate]);

        $query = $entityManager->createQueryBuilder();
        $query  ->select('o')
                ->from('App\Entity\Order', 'o');

        if ($endDate !== null) {
            $query->where('o.orderedAt BETWEEN :startDate AND :endDate')
                  ->setParameters(['startDate' => $startDate, 'endDate' => $endDate]);
        } else {
            $query->where('o.orderedAt > :startDate')
                  ->setParameter('startDate', $startDate);
        }

        if ($orderBy !== 'ordered') {
            switch ($orderBy) {
                case 'user':
                    $query->join('App\Entity\User', 'u')
                          ->orderBy('u.lastname', $sort);
                    break;
                case 'paiement':
                    $query->orderBy('o.paidAt', $sort);
                    break;
                case 'delivered':
                    $query->orderBy('o.deliveredAt', $sort);
                    break;                
                default:
                    $query->orderBy('o.orderedAt', $sort);
                    break;
            }
        } else {
            $query->orderBy('o.orderedAt', $sort);
        }

                // dd($query->getDQL());
        return $query->getQuery()->getResult();
    }

    public function getSortedOrders(string $orderBy, string $sort = 'ASC')
    {
        $entityManager = $this->getEntityManager();

        $sort = $sort !== 'ASC' && $sort !== 'DESC' ? 'ASC' : $sort;

        $query = $entityManager->createQueryBuilder();
        $query  ->select('o')
                ->from('App\Entity\Order', 'o');

        if ($orderBy !== 'ordered') {
            switch ($orderBy) {
                case 'user':
                    $query->join('App\Entity\User', 'u')
                            ->orderBy('u.lastname', $sort);
                    break;
                case 'paiement':
                    $query->orderBy('o.paidAt', $sort);
                    break;
                case 'delivered':
                    $query->orderBy('o.deliveredAt', $sort);
                    break;                
                default:
                    $query->orderBy('o.orderedAt', $sort);
                    break;
            }
        } else {
            $query->orderBy('o.orderedAt', $sort);
        }

                // dd($query->getDQL());
        return $query->getQuery()->getResult();
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
