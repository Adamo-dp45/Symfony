<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/connect/github', name: 'github_connect')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        // ClientRegistry : Factory qui va permettre de récupérer n'importe quel client
        /**
         * @var \KnpU\OAuth2ClientBundle\Client\Provider\GithubClient
         */
        $client = $clientRegistry->getClient('github');
        return $client->redirect([ // Permet de générer un redirect response
            'read:user', // scopes pemret d'indiquer ce qu'on veut récupérer sur l'utilisateur, on peut les récupérer sur leur doc
            'user:email'
        ]);
    }

    #[Route(path: '/oauth/check/{service}', name: 'oauth_check')]
    public function oauth(Request $request, ClientRegistry $clientRegistry)
    {
        return new Response('Ok', 200);
        // dd($clientRegistry); -- Voir doc si on veut faire le traitement via ce controller, ici nous on utilise GithubAuthenticator

        /*
         * @var \KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient
         *
        $client = $clientRegistry->getClient('google');
        try {
            $googleUser = $client->fetchUser();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_login');
        }
        $email = $googleUser->getEmail();
        /**
         * @var User
         *
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            $user = new User();
            $user
                ->setEmail($email)
                ->setUsername($googleUser->getName());
            $em->persist($user);
            $em->flush();
        }

        $this->get('security.token_storage')->setToken( -- Connexion manuelle de l'utilisateur
            new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
                $user,
                null,
                'main',
                $user->getRoles()
            )
        );
        return $this->redirectToRoute('home');
        */
    }

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])] // ApiPlatform 'jwt' mais un 'firewalls' va agir dessus
    public function jsonLogin(): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        return $this->json([
           'username' => $user->getUsername(),
           'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/login/json', name: 'login_json', methods: ['POST'])] // ApiPlatform 'json'
    public function jsonLoginMe(): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        return $this->json([
           'username' => $user->getUsername(),
           'roles' => $user->getRoles()
        ]);
    }


    #[Route('/reset-password', name: 'forgot')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            /**
             * @var User
             */
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetRequestedAt(new \DateTimeImmutable());
                $em->flush();

                $resetLink = $this->generateUrl('reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $message = (new Email())
                    ->from('damo45745@gmail.com')
                    ->to($email)
                    ->subject('Réinitialisation de votre mot de passe')
                    ->html("<p>Bonjour, cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>$resetLink</a></p>");
                $mailer->send($message);
            }
            $this->addFlash('success', 'Si l’email existe, un lien de réinitialisation a été envoyé');
        }
        return $this->render('security/reset.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'reset')]
    public function reset(string $token, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        /**
         * @var User
         */
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetRequestedAt() < new \DateTimeImmutable('-30 minutes')) {
            throw $this->createNotFoundException('Lien invalide ou expiré.');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $user->setResetToken(null);
            $user->setResetRequestedAt(null);

            // Débloquer le compte
            // $user->setLoginAttempts(0);
            // $user->setIsBlocked(false);
            // $user->setLastLoginAttemptAt(null);

            $em->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password.html.twig', [
            'token' => $token,
        ]);
    }

}
