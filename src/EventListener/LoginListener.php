<?php

namespace App\EventListener;

use App\Entity\LoginHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class LoginListener
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RequestStack $requestStack
    )
    {
    }

    #[AsEventListener(event: 'security.interactive_login')]
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // ... Système d'historique de connexion
        /*
            $user = $event->getAuthenticationToken()->getUser();
            $request = $this->requestStack->getCurrentRequest();
            $ip = $request?->getClientIp() ?? 'unknown';

            $login = new LoginHistory();
            $login->setUser($user);
            $login->setLoginAt(new \DateTimeImmutable());
            $login->setIpAddress($ip);

            $this->em->persist($login);
            $this->em->flush();
        */
    }

    /* -- Enregistre le Listener dans services.yaml ou utiliser l'attribut AsEventListener
        App\EventListener\LoginListener:
            tags:
                - { name: kernel.event_listener, event: security.interactive_login }
    */
}
