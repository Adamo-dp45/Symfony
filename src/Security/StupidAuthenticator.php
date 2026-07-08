<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Permet de se connecter via '?auth=id'
 */
class StupidAuthenticator extends AbstractAuthenticator // Dans 'security.yaml'
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supports(Request $request): bool|null
    {
        return $request->query->get('auth', 'auth'); /*
            - On supporte si cette clé est dans la requête, default 'auth' sinon vas m'afficher une erreur si je n'est pas de params et pour se connecter on mais dans l'url '?auth=id'
        */
    }

    public function authenticate(Request $request): Passport
    {
        $userId = $request->query->get('auth', 'auth'); /*
            - On a directement authentifié l'utilisateur vu qu'on n'attend pas un mot de passe
        */
        return new SelfValidatingPassport(
            new UserBadge($userId, function(string $userId) {
                return $this->userRepository->find($userId);
            }) /*
                - Va prendre en 1er params des 'credentials', mais ici il ne va pas fonctionner car il s'attend à un 'email' donc il peut prendre un loader pour charger l'utilisateur
            */
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): Response|null
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response|null
    {
        return null;
    }
}