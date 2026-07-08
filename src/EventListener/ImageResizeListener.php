<?php

namespace App\EventListener;

use App\Entity\Picture;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Vich\UploaderBundle\Event\Event;

/**
 * Permet de redimensionner une image à travers vich
 */
final class ImageResizeListener
{
    #[AsEventListener] // (event: 'vich_uploader.post_upload', method: 'onPostUpload') - On peut faire ça au lieu du service.yaml
    public function onUpload(Event $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof Picture) {
            return;
        }

        $mapping = $event->getMapping();
        $filePath = $mapping->getUploadDestination() . '/' . $object->getFilename(); // Récupère le chemin du fichier uploadé
        // $file = md5(uniqid(rand(), true)) . 'webp';

        // --- Intervention image --- //
        $manager = new ImageManager(new Driver());
        $image = $manager->read($filePath);
        $image->cover(1000, 600);
        // $image->resize(400, 400); -- Redimensionnement exacte, ou (w, null) ajuste une seule dimension avec ratio
        // $image->scale(width: 400, height: 400); -- Redimensionne + recadre pour remplir
        // $image->crop(400, 400, 0, 0, 'FFFFFF', 'top-left'); -- Découper un morceau précis
        $image->save($filePath);

        // --- Imagine --- //
        // $imagine = new Imagine();
        // $image = $imagine->open($filePath); -- Ouvrir une image
        // $image
        //    ->resize(new Box(1000, 800)) -- Redimensionner à une taille exacte
        //    ->thumbnail(new Box(1000, 800), ImageInterface::THUMBNAIL_INSET) -- Redimensionner en gardant les proportions et en respectant un mode (inset ou outbound)
        //    ->crop(new Point(50, 50), new Box(300, 200)) -- Découper une partie de l’image, position de départ (x, y) - taille de la zone à garder
        //    ->resize($image->getSize()->scale(0.5)) -- Redimensionner selon un facteur d’échelle
        //   ->save($filePath, ['quality' => 80]);

        /* --- Fixer la largeur à 400px et calculer la hauteur en gardant le ratio.
        $size = $image->getSize();
        $ratio = $size->getHeight() / $size->getWidth();
        $newWidth = 400;
        $newHeight = intval($newWidth * $ratio);
        $image->resize(new Box($newWidth, $newHeight))
        */
    }

    // App\EventListener\ImageResizeListener: -- service.yaml
    //    tags:
    //        - { name: kernel.event_listener, event: vich_uploader.post_upload, method: onUpload }
    // Dans le service il s'attend a avoir la méthode onVichUploaderPostUpload ou soit la class soit invocable `__invoke` ou préciser la méthode
}
