<?php

namespace App\Domain\Helper;

use App\Entity\User;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Hmac\Sha384;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureCookieGenerator { // Dans service.yaml

    public function __construct(
        private string $secret
    )
    {
    }

    public function generate(User $user)
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->secret)
        );

        $token = $config->builder()
            ->withClaim('mercure', [
                'subscribe' => [ // 'subscribe' pour la lecture, 'publish' si on veut publier
                    "http://monsite.com/user/{$user->getId()}"
                ]
            ]) // Pour définir la structure du token et la cible sur lequel on écoute
            ->getToken(
                $config->signer(), // !! signé le token
                $config->signingKey()
            );
        /*
            - 'Path' le chemin du cookie mais ne sera nécéssaire que pour la partie hub
            - 'HttpOnly' pour ne pas que le javascript puisse le lire
            - 'Secure' si on'est en https
        */
        return sprintf(
            'mercureAuthorization=%s; Path=/.well-known/mercure; HttpOnly; SameSite=Strict',
            $token->toString()
        );
    }
}