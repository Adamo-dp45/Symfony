<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Symfony\Component\Workflow\TransitionBlocker;

class BlogPublishingSubscriber implements EventSubscriberInterface
{
    public function handleReview(TransitionEvent $event): void
    {
        /**
         * @var Post
         */
        $post = $event->getSubject();
        // $post->setReviewedAt(new DateTime()); -- Si on passe dans la transition 'to_review' la date de review de cette article sera mis à jour
    }

    public function guardPublish(GuardEvent $event)
    {
        // $event->setBlocked(true, 'Je ne peut pas :('); -- Ou le faire dans 'workflow.yaml' - 'guard'

        $transition = $event->getTransition();
        [$min, $max] = $event->getMetadata('hours', $transition); // Permet de récupérer les metadata de cette transition
        if(date('H') < $min || date('H') > $max) {
            $event->setBlocked(true, 'Vous ne pouvez pas publier dans ces heures là');
            /*
                $event->addTransitionBlocker( -- Second manière de bloquer
                    new TransitionBlocker('Vous ne pouvez pas publier dans ces heures là', 0) -- 0 param 2 - un code
                );
            */
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.blog_publishing.transition.to_review' => 'handleReview', // Pour le nommage, doc
            'workflow.blog_publishing.guard.publish' => 'guardPublish', // Va m'empêcher d'aller à l'état 'reviewed' si je ne rempli pas les conditions
        ];
    }
}
