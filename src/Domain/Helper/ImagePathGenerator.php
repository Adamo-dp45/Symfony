<?php

namespace App\Domain\Helper;

use League\Glide\Urls\UrlBuilder;
use League\Glide\Urls\UrlBuilderFactory;

class ImagePathGenerator
{
    private UrlBuilder $urlBuilder;

    public function __construct(string $signature) // Pour l'utiliser de cette manière définir dans le service.yaml comment le créer
    {
        $this->urlBuilder = UrlBuilderFactory::create('/images/', $signature); // Chemin de base pour les images
    }

    public function generate(?string $path, int $width, int $height): string
    {
        if($path === null || empty($path)) {
            return '';
        }

        // $urlBuilder = UrlBuilderFactory::create('/image/', ); -- Chemin de base pour les images
        return $this->urlBuilder->getUrl($path, ['w' => $width, 'h' => $height, 'fit' => 'crop']);
    }
}