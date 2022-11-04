<?php

namespace App\Repository;

use App\Entity\Depot;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Depot>
 *
 * @method Depot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depot[]    findAll()
 * @method Depot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depot::class);
    }

    public function add(Depot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Depot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllOrdersByDepot(DateTimeImmutable $dateStart, ?DateTimeImmutable $dateEnd)
    {
        $em = $this->getEntityManager();

        $query = $em->createQueryBuilder();
        $query->select('d, o')
              ->from('App\Entity\Depot', 'd')
              ->join('d.orders', 'o')
              ->where('o.orderedAt >= :dateStart');

        if ($dateEnd !== null) {
            $query->andWhere('o.orderedAt <= :dateEnd')
                  ->setParameter('dateEnd', $dateEnd);
        }
        $query->setParameter('dateStart', $dateStart);

        return $query->getQuery()->getResult();
    }
}
