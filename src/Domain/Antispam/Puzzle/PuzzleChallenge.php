<?php

namespace App\Domain\Antispam\Puzzle;

use App\Domain\Antispam\ChallengeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PuzzleChallenge implements ChallengeInterface {

    public const WIDTH = 350;
    public const HEIGHT = 200;
    public const PIECE_WIDTH = 80;
    public const PIECE_HEIGHT = 50;
    private const SESSION_KEY = 'puzzles';
    private const PRECISION = 2; // On autorise 2px de décalage du puzzle

    public function __construct(
        private readonly RequestStack $requestStack // 'RequestStack' vu que symfony n'a pas d'injection pour la session
    )
    {
    }

    /**
     * Permet de générer la clé
     * @return int
     */
    public function generateKey(): string
    {
        $session = $this->getSession();
        $now = time();
        $x = mt_rand(0, self::WIDTH - self::PIECE_WIDTH); // 'mt_rand' permet de générer un nombre aléatoire
        $y = mt_rand(0, self::HEIGHT - self::PIECE_HEIGHT);
        $puzzles = $session->get(self::SESSION_KEY, []); // Vu que l'utilisateur peut charger plusieurs formulaires dans différents onglets on va sauvegarder en session plusieurs puzzle
        $puzzles[] = [
            'key' => $now,
            'solution' => [$x, $y]
        ];
        $session->set(self::SESSION_KEY, array_slice($puzzles, -10)); // 'array_slice' pour éviter de charger la session si l'utilisateur charge trop de formulaire
        return $now;
    }

    public function verify(string $key, string $answer): bool
    {
        $expected = $this->getSolution($key);
        if(!$expected) {
            return false;
        }

        // On supprime le puzzle dépuis la session
        $session = $this->getSession();
        $puzzles = $session->get(self::SESSION_KEY);
        $session->set(self::SESSION_KEY, array_filter($puzzles, fn(array $puzzle) => $puzzle['key'] !== intval($key)));

        $got = $this->stringToPosition($answer); // La reponse donné par l'utilisateur
        return abs($expected[0] - $got[0]) <= self::PRECISION && abs($expected[1] - $got[1]) <= self::PRECISION; // 'abs' valeur absolue
    }

    /**
     * Permet de récupérer la reponse à partir d'une clé
     * @param string $key
     * @return int[]|null
     */
    public function getSolution(string $key): array|null
    {
        $puzzles = $this->getSession()->get(self::SESSION_KEY, []);
        foreach($puzzles as $puzzle) {
            if($puzzle['key'] !== intval($key)) { // Si ne correspond pas
                continue;
            }
            return $puzzle['solution'];
        }
        return null;
    }

    /**
     * 
     * @param string $s
     * @return int[]
     */
    public function stringToPosition(string $s): array
    {
        $parts = explode('-', $s, 2); // On sépare et avec une limite de 2
        if(count($parts) !== 2) {
            return [-1, -1];
        }
        return [intval($parts[0], intval($parts[1]))]; // La valeur entière de la n partie de notre chaîne
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getMainRequest()->getSession();
    }
}