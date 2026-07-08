<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class APIKeyAuthenticator extends AbstractAuthenticator // security.yaml
{
    /**
     * Permet de démarrer une authentification si l'utilisateur n'est pas connecté et essai d'acceder à une ressource limité
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $authException
     * @return JsonResponse
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse(['error' => 'Auth header required'], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Renvoi un boolean pour dire si on supporte une authentification
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function supports(Request $request): ?bool
    {
        $auth = $request->headers->get('Authorization');
        return $auth && str_starts_with($auth, 'Bearer');
    }

    /**
     * Estirpe dépuis la requête les données
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array{apiToken: array|string}
     */
    public function getCredentials(Request $request)
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        return [
            'apiToken' => $token
        ];
    }

    /**
     * Renvoi un utilisateur à partir des credentials
     */
    public function getUser($credentials, UserProviderInterface $userProviderInterface)
    {
        return $userProviderInterface->loadUserByIdentifier($credentials['apiToken']);
    }

    /**
     * Vérifie que ce qu'on a passé en paramètre est existant
     * @param mixed $credentials
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user instanceof User && $credentials['apiToken'] === $user->getApiToken();
    }

    /**
     * Si la clé de l'utilisateur n'est pas valide
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => 'Apikey invalide'
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * Ici on authentifie l'utilisateur
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SelfValidatingPassport
     */
    public function authenticate(Request $request): Passport
    {
        $identifier = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        return new SelfValidatingPassport(
            new UserBadge($identifier)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Si l'utilisateur est authentifié
    }

    public function supportsRememberMe()
    {
        return false;
    }
}