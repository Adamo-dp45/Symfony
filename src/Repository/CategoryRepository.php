<?php

namespace App\Repository;

use App\Entity\Category;
use App\DTO\CategoryWithCountDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return CategoryWithCountDTO[]
     */
    public function findAllWithCount(): array
    {
        return $this->createQueryBuilder("c")
            // ->select("c as category", 'COUNT(c.id) as total')
            ->select('NEW App\\DTO\\CategoryWithCountDTO(c.id, c.name, COUNT(c.id))') // Spécifique à doctrine (avancée)
            ->leftJoin('c.recipes', 'r') // En fesant ça, il utilise la liaison automatiquement
            ->where('c.deleteAt IS NULL') // Pour la corbeille
            ->groupBy('c.id')
            ->getQuery()
            ->getResult()
        ;

        # Ou faire du DQL
        /*
            $this->getEntityManager()->createQuery(<<<DQL
                SELECT c.id, COUNT(c.id), c.name
                FROM App\Entity\Category as c
                LEFT JOIN c.recipes r
                GROUP BY c.id
            DQL)->getResult();
        */
        // Le $this..->createNativeQuery() permet de créer du sql brute
    }

    public function searchBy(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Corbeille
     * @return array
     */
    public function findByDelete(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.deleteAt IS NOT NULL')
            ->orderBy('c.deleteAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Conte le nombre d'éléments dans la corbeille
     * @return bool|float|int|string|null
     */
    public function countTrashed(): int
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.deleteAt IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
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

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
