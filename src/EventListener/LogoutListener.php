<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener
{
    #[AsEventListener]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        if(in_array('application/json', $event->getRequest()->getAcceptableContentTypes())) {
            $event->setResponse(new JsonResponse(null, Response::HTTP_NO_CONTENT)); // ApiPlateform
        }
    }
}
