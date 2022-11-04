<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findNbProducts(string $name, int $quantityUnity, string $unity)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT COUNT(p.id) total
            FROM App\Entity\Product p
            WHERE p.name = :name AND p.quantityUnity = :quantityUnity AND p.unity = :unity
            '
        )->setParameters([
            'name' => $name,
            'quantityUnity' => $quantityUnity,
            'unity' => $unity
        ]);

        return $query->getSingleResult();
    }

    public function findByCategory($slug): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            '
            SELECT p
            FROM App\Entity\Product p
            JOIN p.category c
            WHERE c.slug = :slug
            '
        )->setParameter('slug', $slug);

        return $query->getResult();
    }
}
