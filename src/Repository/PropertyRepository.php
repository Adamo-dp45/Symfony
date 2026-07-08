<?php

namespace App\Repository;

use App\Entity\Property;
use App\DTO\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * Summary of findAllVisible
     * @return array
     */
    public function findAllVisible(): array
    {
        return $this->createQueryBuilder('p')
            // ->select('p', 'pics')
            // ->leftJoin('p.pictures', 'pics') - Marche bien sur les ManyToOne, mais sur les OneToMany ça peut donner des duplications
            // 3 : Utiliser le cache Twig cache extension
            // 4 : Faire Manuellement --
            ->where('p.sold = false')
            ->getQuery()
            ->getResult();
    }

    public function findAllVisibleQuery(PropertySearch $search)
    {
        $query = $this->createQueryBuilder('p')
        ->where('p.sold = false');

        if($search->getMaxPrice()) {
            $query = $query
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $search->getMaxPrice());
        }
        if($search->getMinSurface()) {
            $query = $query
                ->andWhere('p.surface <= :minSurface')
                ->setParameter('minSurface', $search->getMinSurface());
        }
        if($search->getOptions()->count() > 0) {
            $k = 0;
            foreach($search->getOptions() as $option) {
                $k++;
                $query = $query
                    ->andWhere(":options MEMBER OF p.options")
                    ->setParameter("options", $option);
            }
        }

        // -- Géo - On vas calculer la distance entre lat et lng (formule Haversine)
        if($search->getLat() && $search->getLng() && $search->getDistance()) {
            $query = $query
                ->select('p') // '6353' rayon de la terre
                ->andWhere('(6353 * 2 * ASIN(SQRT( POWER(SIN((p.latitude - :lat) * pi()/180 / 2), 2) +COS(p.latitude * pi()/180) * COS(:lat * pi()/180)  * POWER(SIN((p.longitude - :lng) * pi()/180 / 2), 2) ))) <= :distance')
                ->setParameter('lng', $search->getLng())
                ->setParameter('lat', $search->getLat())
                ->setParameter('distance', $search->getDistance())
            ;
        }

        return $query->setMaxResults(25)->getQuery()->getResult();
    }

    public function findAllLatest(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Property[] Returns an array of Property objects
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

    //    public function findOneBySomeField($value): ?Property
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
