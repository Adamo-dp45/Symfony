<?php

namespace App\Serializer;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class FileDenormalizer implements DenormalizerInterface // Nous permet d'utiliser '$populated' dans 'DesarializeListener' 
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $data; /*
            - Vu qu'on ne peut pas déserializer une image, on n'a considéré que tous ce qui est type 'UploadedFile' soit comme un type primaire et qu'il passe au travers de la déserialization
        */
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $data instanceof File; // Vu qu'on sait que 'UploadedFile' étend de lui et on supporte si l'objet de donnée est une instance
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Post::class => true
        ];
    }
}