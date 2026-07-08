<?php

namespace App\Serializer;

use App\Entity\Post;
use App\Security\Voter\PostVoter;
use App\Serializer\Attribute\ApiAuthGroups;
use ArrayObject;
use ReflectionClass;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PostAPINormalizer implements NormalizerInterface /*, NormalizerAwareInterface */ // services.yaml
{
    // use NormalizerAwareTrait;

    private const ALLREADY_CALLED_NORMALIZER = 'PostAPINormalizerAllreadyCalled';

    private static array $attributeCache = []; // Cache

    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private NormalizerInterface $normalizer // service.yaml ou .. #[Autowire(service: 'serializer.normalizer.object')]
    )
    {
    }

    /**
     * Permet d'indiquer si on supporte la normalisation
     * @param mixed $data
     * @param mixed $format
     * @param array $context
     * @return never
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        // if(!is_object($data)) { -- :)
        //    return false;
        // }
        // $class = new ReflectionClass(get_class($data)); -- Avec le ApiAuthGroups
        // $classAttributes = $class->getAttributes(ApiAuthGroups::class);
        // $allreadyCalled = $context[self::ALLREADY_CALLED_NORMALIZER] ?? false;
        // return $allreadyCalled === false && !empty($classAttributes);

        // ^^ Optimiser
        /* Ecriture
        if(($context[self::ALLREADY_CALLED_NORMALIZER] ?? false) === true) {
            return false;
        }
        $class = new ReflectionClass(get_class($data));
        return !empty($class->getAttributes(ApiAuthGroups::class));
        */
        if (!is_object($data) || ($context[self::ALLREADY_CALLED_NORMALIZER] ?? false)) { // Cache
            return false;
        }

        return $this->hasApiAuthGroupsAttribute($data);

        // $allreadyCalled = $context[self::ALLREADY_CALLED_NORMALIZER] ?? false; -- Sans le ApiAuthGroups
        // return $data instanceof Post && $allreadyCalled === false;
    }

    /**
     * Permet de normaliser les données
     * @param mixed $data
     * @param mixed $format
     * @param array $context
     * @return null
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        // Il faudra optimiser ou separer dans une class et utiliser du cache pour éviter de faire ça systématiquement, Ici on le fais pour chaque élément
        // $class = new ReflectionClass(get_class($data)); -- :)
        // $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();
        // foreach($apiAuthGroups->groups as $role => $groups) {
        //    if($this->authorizationChecker->isGranted($role, $data)) {
        //        $context['groups'] = array_merge($context['groups'] ?? [], $groups);
        //    }
        // }
        // $context[self::ALLREADY_CALLED_NORMALIZER] = true;
        // return $this->normalizer->normalize($data, $format, $context);

        // ^^ Optimiser
        /* -- Ecriture
        $class = new ReflectionClass(get_class($data));
        $attributes = $class->getAttributes(ApiAuthGroups::class)[0] ?? null;
        if($attributes) {
            $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();
            foreach($apiAuthGroups->groups as $role => $groups) {
                if($this->authorizationChecker->isGranted($role, $data)) {
                    $context['groups'] = array_merge($context['groups'] ?? [], $groups);
                }
            }
        }
        */
        $attribute = $this->getApiAuthGroupsAttribute($data); // Cache
        if ($attribute !== null) {
            foreach ($attribute->groups as $role => $groups) {
                if ($this->authorizationChecker->isGranted($role, $data)) {
                    $context['groups'] = array_merge($context['groups'] ?? [], $groups);
                }
            }
        }
        $context[self::ALLREADY_CALLED_NORMALIZER] = true;
        return $this->normalizer->normalize($data, $format, $context);

        // $context[self::ALLREADY_CALLED_NORMALIZER] = true; // Sans le ApiAuthGroups
        // if($this->authorizationChecker->isGranted(PostVoter::EDIT, $data) && isset($context['groups'])) {
        //    $context['groups'][] = 'read:collection:User';
        // }
        // return $this->normalizer->normalize($data, $format, $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Post::class => true // Sera activer si on n'est dans une normalisation d'un Post
        ];
    }

    /**
     * Cache
     */
    private function hasApiAuthGroupsAttribute(object $data): bool
    {
        $className = get_class($data);
        if (!array_key_exists($className, self::$attributeCache)) {
            $refClass = new ReflectionClass($className);
            $attributes = $refClass->getAttributes(ApiAuthGroups::class);
            self::$attributeCache[$className] = count($attributes) > 0 ? $attributes[0]->newInstance() : null;
        }

        return self::$attributeCache[$className] !== null;
    }

    /**
     * Cache
     */
    private function getApiAuthGroupsAttribute(object $data): ?ApiAuthGroups
    {
        $this->hasApiAuthGroupsAttribute($data); // initialise le cache si besoin
        return self::$attributeCache[get_class($data)] ?? null;
    }
    // On peut le remplacer par un vrai cache Symfony, mais ce n’est généralement utile que si tu traites des milliers d’objets et que tu veux garder le cache entre requêtes HTTP
}

// Pour faire de sorte que l'utilisateur puisse modifier des champs en fonction de son rôle, c'est le même principe mais on vas utiliser le denormalizer