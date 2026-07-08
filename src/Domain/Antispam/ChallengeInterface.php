<?php

namespace App\Domain\Antispam;

interface ChallengeInterface
{
    /**
     * Permet de générer une clé
     * @return void
     */
    public function generateKey(): string;

    /**
     * Permet de valider la solution
     * @param string $key
     * @param string $answer
     * @return void
     */
    public function verify(string $key, string $answer): bool;

    /**
     * Permet de générer le problème à partir de la clé et à partir de la clé être capable d'obtenir la solution attendu par l'utilisateur
     * @param string $key
     * @return void
     */
    public function getSolution(string $key): mixed;

}