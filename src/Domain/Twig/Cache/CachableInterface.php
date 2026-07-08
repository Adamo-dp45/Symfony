<?php

namespace App\Domain\Twig\Cache;

/**
 * Permet d'expliquer quel sont les éléments nécéssaire pour qu'un élément soit mis en cache
 */
interface CachableInterface
{
    public function getId(): int;

    public function getUpdatedAt(): \DateTimeInterface;
}