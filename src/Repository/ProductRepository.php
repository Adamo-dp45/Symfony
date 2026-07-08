<?php

namespace App\Repository;

use App\DTO\ProductSearch;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * Récupère les produits en lien avec une recherche
     * @return PaginationInterface
     */
    public function findSearch(ProductSearch $search): PaginationInterface
    {
        // Le true : Je veux que tu ignore les filtres concernant le prix
        $query = $this->getQuerySearch($search, true)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            4
        );
    }

    /**
     * Récupère le prix minimum et maximum correspondant à une recherche
     * @param ProductSearch $search
     * @return int[]
     */
    public function findMinMax(ProductSearch $search): array
    {
        $result = $this->getQuerySearch($search, true) // Ignore les filtres conçernant le prix
            ->select('MIN(p.price) as min', 'MAX(p.price) as max')
            ->getQuery()
            ->getScalarResult(); // Pour récupérer les données sur une ligne
        return [(int)$result[0]['min'], (int)$result[0]['max']];
    }

    private function getQuerySearch(ProductSearch $search, $ignorePrice = false): ORMQueryBuilder // ignorePrice pour le min et max
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.categories', 'c'); // Récupera toutes les infos en une seule requête

        if(!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if(!empty($search->min) && $ignorePrice === false) { // && $ignorePrice === false - Cette condition est pertinante dans le liting des produits mais pas quand on veut récupérer le min et le max
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $search->min);
        }

        if(!empty($search->max) && $ignorePrice === false) { // -
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $search->max);
        }

        if(!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if(!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }
        return $query;
    }

    public function findBySearchQuery(string $query, int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
