<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class NotVerifiedEmailException extends CustomUserMessageAccountStatusException // A pour particulariter de retransmettre le message dans la session
{
    public function __construct(
        string $message = 'Ce compte ne semble pas posséder d\'email vérifié', // Ce message sera transmi automatiquement lorsqu'on a des erreurs, au niveau du SecurityController grâce à la méthode ->getLastAuthenticationError et le message sera affiché au niveau de la page login
        array $messageData = [],
        int $code = 0,
        \Throwable|null $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}