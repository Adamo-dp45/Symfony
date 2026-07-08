<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Post;
use App\Entity\User;
use ReflectionClass;
use Symfony\Bundle\SecurityBundle\Security;

class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface /*
    - En implémentant 'QueryCollectionExtensionInterface' ça va automatiquement rajouté le bon service et rajouté l'extension à la liste des extensions qui sont disponible au niveau de 'ApiPlatform'
        - 'applyToCollection' va limité au niveau de la collection 'getCollection'
    - 'QueryItemExtensionInterface' permet de récupérer un item en particulier
        - 'applyToItem' va limité au niveau d'un item 'get', 'put', 'patch' et 'delete' mais pas 'post'
*/
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void
    {
        $this->addWhere($resourceClass, $queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void
    {
        /*
            - if($operation->getName() === 'trash_get_collection') return -- On vérifié le nom de l'opération si on l'a nommé
        */
        $this->addWhere($resourceClass, $queryBuilder);
    }

    private function addWhere(string $resourceClass, QueryBuilder $queryBuilder)
    {
        $reflectionClass = new ReflectionClass($resourceClass); // Peut être impactant, donc on peut le mettre en cache

        // if($resourceClass === Post::class) { -- On peut vérifier pour une class
        if($reflectionClass->implementsInterface(UserOwnedInterface::class)) { // Ou une interface, plus générique car il faudre juste l'implémenté
            /**
             * @var User
             */
            $user = $this->security->getUser();
            return; // On l'a commenté à cause du de l'attribut 'ApiAuthGroups' qu'on a crée pour qu'on puisse voir tous les articles
            /*
                if($user) { -- L'utilisateur ne voit que ses ressources étant connecté
                    $alias = $queryBuilder->getAllAliases()[0]; // Récupère le premier alias, alias racine
                    $queryBuilder
                        ->andWhere("$alias.user = :current_user")
                        ->setParameter('current_user', $user->getId());
                } else { -- Sinon l'utilisateur à accès aux ressources qui snt publique
                    $alias = $queryBuilder->getAllAliases()[0];
                    $queryBuilder
                        ->andWhere("$alias.user IS NULL");
                }
            */
        }
    }
}