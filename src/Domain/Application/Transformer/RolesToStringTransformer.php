<?php

namespace App\Domain\Application\Transformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\DataTransformerInterface;

class RolesToStringTransformer implements DataTransformerInterface {

    /**
     * Transforme un tableau en chaîne de caractère
     * @param mixed $roles
     * @return string|null
     */
    public function transform($roles): string|null
    {
        if (is_array($roles) && !empty($roles)) {
            return $roles[0]; // On ne prend que le premier rôle
        }
        return null;
    }

    /**
     * Transforme une chaîne de caractère en tableau
     * @param mixed $role
     * @return string[]
     */
    public function reverseTransform($role): array
    {
        if (is_string($role)) {
            return [$role];
        }
        return [];
    }

}