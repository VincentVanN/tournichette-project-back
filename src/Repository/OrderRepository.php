<?php

namespace App\Repository;

use App\Entity\Depot;
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

    /**
     * Find orders with filters in parameters
     * 
     * @param string $startDate The start date
     * @param string $endDate The final date
     * @param string $orderBy The ordering parameter
     * @param string $sort The 'ASC or 'DESC' sorting parameter
     * @param Depot $depot The depot of orders
     * 
     */
    public function findWithMultiFilters(string $startDate, string $endDate = null, string $orderBy = 'orderedAt', string $sort = 'ASC', Depot $depot = null)
    {
        $entityManager = $this->getEntityManager();

        $sort = $sort !== 'ASC' && $sort !== 'DESC' ? 'ASC' : $sort;

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

        if ($depot !== null) {
            $query->andWhere('o.depot = :depot')
                  ->setParameter('depot', $depot);
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

        return $query->getQuery()->getResult();
    }

    /**
     * Find orders with filters in parameters
     * 
     * @param string $orderBy The ordering parameter
     * @param string $sort The 'ASC or 'DESC' sorting parameter
     * @param Depot $depot The depot of orders
     * 
     */
    public function getSortedOrders(string $orderBy, string $sort = 'ASC', Depot $depot = null)
    {
        $entityManager = $this->getEntityManager();

        $sort = $sort !== 'ASC' && $sort !== 'DESC' ? 'ASC' : $sort;

        $query = $entityManager->createQueryBuilder();
        $query  ->select('o')
                ->from('App\Entity\Order', 'o');

        if ($depot !== null) {
            $query->andWhere('o.depot = :depot')
                  ->setParameter('depot', $depot);
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
}
