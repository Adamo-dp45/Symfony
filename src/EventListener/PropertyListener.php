<?php

namespace App\EventListener;

use App\Entity\Property;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class PropertyListener // Ensuite service.yaml, pas nécéssaire grâce à l'attribut AsEventListener
{
    public function __construct(
        private readonly Filesystem $filesystem
    )
    {
    }

    #[AsEventListener]
    public function onResponseEvent(LifecycleEventArgs $args): void
    {
        if(!$args->getObject() instanceof Property) {
            return; // Par défaut il se déclanche à tout moment
        }

        $uploadDir = '';
        // foreach ($args->getObject()->getPictures() as $picture) {
        //    $filePath = $uploadDir . '/' . $picture->getFilename();
        //    if ($this->filesystem->exists($filePath)) {
        //        $this->filesystem->remove($filePath);
        //    }
        // }
    }
}
