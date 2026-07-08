<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

final class LoginFailureListener
{
    #[AsEventListener(event: LoginFailureEvent::class)]
    public function onSecurityInteractiveLogin(LoginFailureEvent $event): void
    {
        // ...
        if(!file_exists('pdf')) {
            mkdir('pdf');
        }
    }
}
