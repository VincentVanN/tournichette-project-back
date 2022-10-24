<?php

namespace App\Repository;

use App\Entity\CartOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CartOrder>
 *
 * @method CartOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartOrder[]    findAll()
 * @method CartOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartOrder::class);
    }

    public function add(CartOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartOrder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByDate(string $date = null)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        $query->select('co')
              ->from('App\Entity\CartOrder', 'co');
        
        if ($date !== null) {
            $query->innerJoin('co.orders', 'o')
                  ->where('o.orderedAt > :date')
                  ->setParameters(['date' => $date]);
        }

        return $query->getQuery()->getResult();

    }

    public function findAllProducts()
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            '
            SELECT co, p
            FROM App\Entity\CartOrder co
            JOIN App\Entity\CartProduct cp
            JOIN App\Entity\Product p
        ');

        return $query->getResult();
    }

//    /**
//     * @return CartOrder[] Returns an array of CartOrder objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CartOrder
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
