<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        /*
            dd(
                $event->getUser(), -- Permet de récupérer l'utilisateur de symfony
                $event->getData(), -- Les données qui vont être envoyés dans le payload
                $event->getHeader() -- Les en têtes
            );
        */
        $data = $event->getData();
        $user = $event->getUser();

        if($user instanceof User) {
            $data['email'] = $user->getEmail();
        }
        // $data['exp'] = 162415455; -- Un teste pour expirer le token
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
