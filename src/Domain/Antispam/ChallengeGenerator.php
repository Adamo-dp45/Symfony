<?php

namespace App\Domain\Antispam;

use Symfony\Component\HttpFoundation\Response;

interface ChallengeGenerator
{
    /**
     * Permet de générer un problème
     * @param string $key
     * @return Response
     */
    public function generate(string $key): Response;
}