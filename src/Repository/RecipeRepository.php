<?php

namespace App\Repository;

use App\DTO\SearchQueryDto;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findTotalDuration()
    {
        return (int)$this->createQueryBuilder('r') /*
            - L'avantage du 'queryBuilder' est qu'il génère les requêtes selon le système de base de données
        */
            ->select('SUM(r.duration) as total')
            ->getQuery()
            // ->getSingleResult(); Renvoi un seule resultat si une doit en avoir une
            ->getSingleScalarResult(); // Si on a un seule champ et on ne veut que la valeur de ce champ la
            // ->getResult() Nous renvoi une recette si dans le select('r'), sinon il nous envoi un tableau
    }

    public function paginateRecipes(int $page, ?int $userId = null, ?int $limit = 10): PaginationInterface
    {
        $builder = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->select('r', 'c')
        ;
        if($userId) {
            $builder = $builder->andWhere('r.user = :user')->setParameter('user', $userId);
        }
        return $this->paginator->paginate(
            $builder,
            $page,
            $limit, // La limit
            [ // Permet de limiter les paramètre sinon l'utilisateur pour injecter des requêtes
                'distinct' => true,
                'sortFieldAllowList' => ['r.id', 'r.title']
            ] 
        );

        ## Pour doctrine et retourne : Paginator
        # paginateRecipes(int $page, int $limit)
        /*
        return new Paginator(
            $this->createQueryBuilder('r')
                ->setFirstResult(($page - 1) * $limit) // Permet de spécifier l'offset
                ->setMaxResults($limit)
                ->getQuery()
                ->setHint(Paginator::HINT_ENABLE_DISTINCT, false), // Si on ne veut pas le SELECT DISTINCT
                false // Dans le constructeur de Pagniator il fait un fetchJoinCollection qui par defaut est a true qui veut s'il fait les requêtes, il récupère les leftjoin
        );
        */
    }

    /**
     * @return Recipe[]
    */
    public function findWithDurationLoverThan(int $duration): array
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'c')
            ->where('r.duration < :duration')
            ->orderBy('r.duration', 'ASC')
            ->leftJoin('r.category', 'c')
            ->setMaxResults(10)
            ->setParameter('duration', $duration)
            ->getQuery()
            ->getResult();
    }

    public function search(SearchQueryDto $dto): array
    {
        $query = $this->createQueryBuilder('r');
        if ($dto->getQuery()) {
            $query->andWhere('r.title LIKE :title')
            ->setParameter('title', '%' . $dto->getQuery() . '%');
        }

        if ($dto->getSortBy()) { // Attention si le champ n'existe pas
            $query->orderBy('r.' . $dto->getSortBy(), $dto->getOrder());
        }

        return $query
            ->setFirstResult(($dto->getPage() - 1) * $dto->getLimit())
            ->setMaxResults($dto->getLimit())
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
