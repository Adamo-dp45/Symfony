<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DogListener # Première manière de faire les choses, Lui il agit sur n'importe quel page à la reponse
{
    private array $badWords = ['con', 'merde', 'idiot', 'putain'];

    #[AsEventListener(event: KernelEvents::RESPONSE)]
    public function onResponseEvent(ResponseEvent $event): void
    {
        // Ici on vas remplacé les occurances de Dog par Cat dans notre contenu
        $event->getResponse()->setContent(str_replace('Dog', 'Cat', $event->getResponse()->getContent()));

        // Ou ..
        /*
            $response = $event->getResponse();
            $content = $response->getContent();
            if (!$content) return;
            foreach ($this->badWords as $badWord) {
                $pattern = '/' . preg_quote($badWord, '/') . '/i';
                $content = preg_replace($pattern, str_repeat('*', strlen($badWord)), $content);
            }
            $response->setContent($content);
        */
    }
}
