<?php

namespace App\EventSubscriber;

use App\Entity\Picture;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/*
    - Avant (Symfony 7) : 'implements EventSubscriber' + getSubscribedEvents() + tag 'doctrine.event_subscriber' dans services.yaml
    - Maintenant : un "listener" Doctrine déclaré par attribut, une ligne par évènement écouté (la méthode porte le nom de l'évènement)
*/
#[AsDoctrineListener(event: Events::preRemove)] // Permet d'écouter un évènement quand une entité est supprimer
#[AsDoctrineListener(event: Events::preUpdate)] // Permet d'écouter un évènement quand une entité est modifier
class ImageCacheSubscriber
{
    public function __construct(
        private CacheManager $cacheManager,
        private UploaderHelper $uploaderHelper
    )
    {
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        if(!$args->getObject() instanceof Property) { // On utilisera Picture au lieu de Property
            return; // Par défaut il se déclanche à tout moment
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($args->getObject(), 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        if(!$args->getObject() instanceof Picture) { // Ou Property
            return; // Par défaut il se déclanche à tout moment
        }

        if($args->getObject()->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($args->getObject(), 'imageFile'));
        }
    }

}
