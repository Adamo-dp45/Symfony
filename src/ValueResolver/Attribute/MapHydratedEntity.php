<?php

namespace App\ValueResolver\Attribute;

use App\ValueResolver\EntityHydratorValueResover;
use Attribute;
use Symfony\Component\HttpKernel\Attribute\ValueResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;

#[Attribute(Attribute::TARGET_PARAMETER)] // Va cibler les paramètre d'une fonction
final class MapHydratedEntity extends ValueResolver
{
    public function __construct(
        public readonly array $groups = [],
        public readonly string|null|GroupSequence|array $validationGroups = null
    )
    {
        parent::__construct(EntityHydratorValueResover::class); // Attend deux params : resolver le nom de la class qui va permettre de faire la resolution, disabled permet de désactiver ou non ce système
    }
    // Ensuite dans RecipeController Api
}