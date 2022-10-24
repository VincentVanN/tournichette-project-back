<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

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

    public function findByDate(string $date = null)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        $query->select('op')
              ->from('App\Entity\OrderProduct', 'op');
        
        if ($date !== null) {
            $query->innerJoin('op.orders', 'o')
                  ->where('o.orderedAt > :date')
                  ->setParameters(['date' => $date]);
        }

        return $query->getQuery()->getResult();

    }

    public function getTotalQuantityByProducts()
    {
        $em = $this->getEntityManager();

        // $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        // $rsm->addRootEntityFromClassMetadata('App\\Entity\\Product', 'p', ['total_product' => 'total']);

        // $sql = "SELECT *,  SUM(op.quantity * p.quantity_unity) AS total_product
        // FROM `order_product` op
        // JOIN `product` p ON op.product_id = p.id
        // GROUP BY p.name, p.unity";

        // $query = $em->createNativeQuery('
        // SELECT *,  SUM(op.quantity * p.quantity_unity) AS total_product
        // FROM `order_product` op
        // JOIN `product` p ON op.product_id = p.id
        // GROUP BY p.name, p.unity
        // ', $rsm);
        
        $query = $em->createQueryBuilder();
        $query->select(['p'])
              ->from('App\Entity\Product', 'p')
              ->groupBy('p.name');

        // dd($query->getQuery());

        // $query = $em->createQuery(
        //     'SELECT p.name, SUM(op.quantity * p.quantityUnity) AS total
        //     FROM App\Entity\OrderProduct op
        //     JOIN App\Entity\Product p
        //     GROUP BY p.name, p.unity
        //     ');

            // dd($query->getQuery()->getResult());
              return $query->getQuery()->getResult();
    }

//    /**
//     * @return OrderProduct[] Returns an array of OrderProduct objects
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

//    public function findOneBySomeField($value): ?OrderProduct
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
