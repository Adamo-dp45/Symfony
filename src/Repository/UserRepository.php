<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUserByEmailOrUsername(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :identifier OR u.username = :identifier')
            ->andWhere('u.isVerified = true')
            ->setParameter('identifier', $usernameOrEmail)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

            // ->andWhere('JSON_CONTAINS(u.roles, :role) = 0')
            // ->setParameter('role', '"ROLE_ADMIN"');
    }

    public function findOrCreateFromGithubOauth(GithubResourceOwner $owner): User // Si je le veut de manière global je peut injecter ResourceOwnerInterface
    {
        /**
         * @var User|null
         */
        $user = $this->createQueryBuilder('u')
            ->where('u.githubId = :githubId')
            ->orWhere('u.email = :email')
            ->setParameter('githubId', $owner->getId())
            ->setParameter('email', $owner->getEmail())
            ->getQuery()
            ->getOneOrNullResult(); // On veut un seule résultat, mais si ça n'existe pas on peut avoir null
        if($user) {
            if($user->getGithubId() === null) { // Veut dire que j'ai trouvé l'utilisateur par rapport a son email dans ma bd
                $user->setGithubId($owner->getId());
                $em = $this->getEntityManager();
                $em->flush();
            }
            return $user;
        }

        $user = new User();
        $user
            ->setRoles([])
            ->setGithubId($owner->getId())
            ->setUsername($owner->getNickname()) // Attention à ne pas que ça rentre en colision avec des username qu'on a déjà dans notre base de donné car ça peut unique
            ->setEmail($owner->getEmail()) // Vérifié si ce n'est pas null
            ->setIsVerified(true)
            ->setPassword($this->passwordHasher->hashPassword($user, '0000'));
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function searchBy(string $search): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.username LIKE :term OR u.email LIKE :term')
            ->setParameter('term', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Utilisateur avec le plus d'articles publiés
     * @return User[]
     */
    public function getUserByProperty(int $limit = 4): array
    {
        return $this->createQueryBuilder('u')
            ->addSelect('COUNT(p.id) as total')
            ->leftJoin('u.posts', 'p')
            ->groupBy('u.id')
            ->orderBy('total', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
