<?php

namespace App\EventSubscriber;

use App\Entity\Picture;
use App\Entity\Property;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber // Ensuite on charge ce subscriber dans le fichier services.yaml
{
    public function __construct(
        private CacheManager $cacheManager,
        private UploaderHelper $uploaderHelper
    )
    {
    }

    public function getSubscribedEvents()
    {
        return [
            // Events::postRemove,
            'preRemove', // Permet d'écouter un évènement quand une entité est supprimer
            'preUpdate' // Permet d'écouter un évènement quand une entité est modifier
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        if(!$args->getObject() instanceof Property) { // On utilisera Picture au lieu de Property
            return; // Par défaut il se déclanche à tout moment
        }
        $this->cacheManager->remove($this->uploaderHelper->asset($args->getObject(), 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        if(!$args->getObject() instanceof Picture) { // Ou Property
            return; // Par défaut il se déclanche à tout moment
        }

        if($args->getObject()->getImageFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($args->getObject(), 'imageFile'));
        }
    }

}
