<?php

namespace App\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Va décorer le contextBuilder qui est déjà présent dans 'ApiPlatform' et on vas charger les groupes de serialization dynamiquement
 */
class PostContextBuilder implements SerializerContextBuilderInterface // services.yaml
{
    public function __construct(private SerializerContextBuilderInterface $decorated, private AuthorizationCheckerInterface $authorizationChecker) // AuthorizationCheckerInterface : Permet juste de savoir si oui ou non on n'a le droit de faire une action
    {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);
        $resourceClass = $context['resource_class'] ?? null;
        if($resourceClass === Post::class && isset($context['groups']) && $this->authorizationChecker->isGranted('ROLE_USER')) {
            $context['groups'][] = 'read:collection:User';
        }
        return $context;
    } // Ce système est intérèssant quand on veut gérer un context générique, mais si on veut le faire élément par élément
}