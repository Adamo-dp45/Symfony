<?php

namespace App\EventSubscriber;

use App\EasyAdmin\TimestampableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function setCreatedAt(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if($entity instanceof TimestampableInterface) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
    }

    public function setUpdatedAt(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();
        if($entity instanceof TimestampableInterface) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['setCreatedAt'],
            BeforeEntityUpdatedEvent::class => ['setUpdatedAt']
        ];
    }
}
