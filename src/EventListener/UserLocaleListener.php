<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Translation\LocaleSwitcher;

final class UserLocaleListener
{
    public function __construct(
        private readonly Security $security, // Faudra faire attention car on vas demarer le Security partout
        private readonly LocaleSwitcher $localeSwitcher
    )
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)] // (priority: ) pour qu'il passe avant dans les event
    public function onRequestEvent(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        if($user && $user instanceof User) {
            $this->localeSwitcher->setLocale($user->getLocale());
        }
    }
}
