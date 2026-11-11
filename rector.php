<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Symfony\Symfony73\Rector\Class_\CommandHelpToAttributeRector;
use Rector\Symfony\Symfony73\Rector\Class_\GetFiltersToAsTwigFilterAttributeRector;
use Rector\Symfony\Symfony73\Rector\Class_\GetFunctionsToAsTwigFunctionAttributeRector;
use Rector\Symfony\Symfony80\Rector\Class_\RemoveEraseCredentialsRector;

/**
 * Rector : réécrit automatiquement le code pour suivre les changements de Symfony.
 *
 * - Prévisualiser : vendor/bin/rector process --dry-run
 * - Appliquer     : vendor/bin/rector process
 *
 * Les "sets" contiennent les règles de migration d'une version de Symfony.
 * On applique ceux entre notre ancienne version (7.3) et la cible (8.x).
 * Toujours relire le diff : Rector est un assistant, pas une garantie.
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/libs',
        __DIR__ . '/tests',
    ])
    ->withSets([
        SymfonySetList::SYMFONY_73, // Voters (argument Vote), contraintes en arguments nommés…
        SymfonySetList::SYMFONY_74,
        SymfonySetList::SYMFONY_80,
    ])
    ->withSkip([
        // Modernisations facultatives (pas des dépréciations) : on garde le code d'apprentissage tel quel
        GetFiltersToAsTwigFilterAttributeRector::class,
        GetFunctionsToAsTwigFunctionAttributeRector::class,
        CommandHelpToAttributeRector::class,
        // Génère une méthode serialize() parasite dans User : eraseCredentials() est retirée à la main
        RemoveEraseCredentialsRector::class,
    ]);
