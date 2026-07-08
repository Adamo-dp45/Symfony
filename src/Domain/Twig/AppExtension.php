<?php

namespace App\Domain\Twig;

use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Attribute\AsTwigFilter;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends TwigExtension {

    /**
     * Permet d'ajouter des fonction personnalisés
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('reverse', [$this, 'reverseText'], ['is_safe' => ['html']]), # Fonction twig, is_safe : On autorise le html
        ];
    }

    /**
    * Ajouter des filtres Twig personnalisés - Pratiques pour transformer des valeurs
    */
    // #[AsTwigFilter('reverse')] -- On peut utiliser les attributs PHP pour déclarer des filtres et fonctions Twig, simplifiant ainsi la création d'extensions
    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']), # Filtre personnalisée
        ];
    }

    /**
    * Ajouter des variables globales accessibles dans tous les templates
    */
    public function getGlobals(): array
    {
        return [
            'name' => 'Mon Application Symfony',
        ];
    }

    public function reverse(string $text): string
    {
        return strrev($text);
    }
}

/*
    config/services.yaml -- Pour enregistrer l'extension comme un service si on n'utilise pas l'autoconfiguration
            services:
                App\Twig\AppExtension:
                        tags: ['twig.extension']
*/