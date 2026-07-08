<?php

namespace App\Controller\Api;

use App\Entity\Post;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsController]
class PostImageController
{
    public function __construct(
        private StorageInterface $storage
    )
    {
    }

    public function __invoke(Request $request): Post
    {
        $post = $request->attributes->get('data');
        if(!$post instanceof Post) {
            throw new RuntimeException('Article attendu');
        }
        $file = $request->files->get('file');
        $post->setImageFile($file);
        $post->setFileUrl($this->storage->resolveUri($post, 'imageFile')); // On doit le dans un normalizer..
        $post->setUpdatedAt(new DateTimeImmutable());
        return $post; /*
            - Va être valider par le système de 'ApiPlatform' et ensuite persisté grâce au 'DataPersister'
        */
    }
}