<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Exception\NotVerifiedEmailException;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class GithubAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface // On enregistre dans security.yaml - custom_authenticator
{
    use TargetPathTrait; // Pour avoir une redirection qui se fait automatiquement dépuis la page ou on voulais acceder

    public function __construct(
        private readonly RouterInterface $router,
        private readonly ClientRegistry $clientRegistry,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * Permet de spécifier comment on vas démarer une authentification, mais ici ce n'est pertinant car je l'utilise dans mon AppAuthenticator
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('app_login'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * Renvoi true si l'authentification doit ce déclenché pour l'url qui est donné
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    public function supports(Request $request): ?bool
    {
        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === 'github'; // On vérifie que la route et le query parameter correspondent
    }

    /**
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return SelfValidatingPassport
     */
    public function authenticate(Request $request): Passport
    {
        /**
         * @var GithubClient
         */
        $client = $this->clientRegistry->getClient('github');
        /**
         * @var \League\OAuth2\Client\Token\AccessToken
         */
        $accessToken = $this->fetchAccessToken($client); // accessToken correspond au crédentials, on pourra récupérer notre utilisateur grâce à lui

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /**
                 * @var GithubResourceOwner -- Contient une réponse qui contient toutes les infos renvoyés par l'api
                 */
                $githubUser = $client->fetchUserFromToken($accessToken);
                // $user_id = $githubUser->getId(); -- Permet d'avoir l'id

                // -- Au par avant dans la reponse Github, l'email etait à null, car github renvoiait l'email public de l'utilisateur or par défaut les utilisateurs n'ont pas d'email public, donc vérifié si on a l'email -- //

                // On récupère l'email de l'utilisateur si besoin en contactant directement l'api avant de renvoyer l'user
                $response = HttpClient::create()->request(
                    'GET',
                    'https://api.github.com/user/emails',
                    [
                        'headers' => [
                            'authorization' => 'token ' . $accessToken->getToken()
                        ]
                    ]
                );
                $emails = json_decode($response->getContent(), true); // On veut un tableau associatif, on aura un tabeleau de taille 2, l'email primaire (privée) et qui est vérifié et l'email qui est généré par github qui n'est pas primaire et qui est vérifié et non visible

                foreach($emails as $email) {
                    if($email['primary'] === true && $email['verified'] === true) {
                        $data = $githubUser->toArray(); // On va recrée un githubUser et modifier pour rajouter un email
                        $data['email'] = $email['email'];
                        $githubUser = new GithubResourceOwner($data);
                    }
                }

                if($githubUser->getEmail() === null) { // On vérifie si l'utilisateur a l'email valide
                    throw new NotVerifiedEmailException();
                }

                return $this->userRepository->findOrCreateFromGithubOauth($githubUser);
            })
        );
    }

    /**
     * Quand tous ce passe bien
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param string $firewallName
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        return new RedirectResponse($targetPath ?: '/fr');
    }

    /**
     * Lorsqu'il y'a une erreur
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Security\Core\Exception\AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        // return new Response($message, Response::HTTP_FORBIDDEN);

        if ($request->hasSession()) { // Pour mettre en session l'erreur d'authentification
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }

}