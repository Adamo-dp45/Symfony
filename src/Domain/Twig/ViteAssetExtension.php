<?php

namespace App\Domain\Twig;

use Psr\Cache\CacheItemPoolInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteAssetExtension extends AbstractExtension
{
    private ?array $manifestData = null; // Pour éviter de lire 2x le fichier 'manisfest.json'
    const CACHE_KEY = 'vite_manifest';

    public function __construct(
        private bool $isDev,
        private string $manifest,
        private CacheItemPoolInterface $cache // Nous permet d'optimiser la lecture et d'éviter le fichier 'manisfest.json' à chaque requête, 'CacheItemPoolInterface' permet de créer un pool spécifique pour notre cache
    )
    { // Dans 'services.yaml' pour cabler
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', [$this, 'asset'], ['is_safe' => ['html']])
        ];
    }

    public function asset(string $entry, array $deps)
    {
        if($this->isDev) {
            return $this->assetDev($entry, $deps);
        }
        return $this->assetProd($entry);
    }

    public function assetDev(string $entry, array $deps): string
    {
        $html = <<<HTML
            <script type="module" src="http://localhost:5173/assets/@vite/client"></script>
HTML;
        if(in_array('react', $deps)) {
           $html .= ''; // Si on a d'autres ressources à chargé
        }
        $html .= <<<HTML
        <script type="module" src="http://localhost:5173/assets/{$entry}" defer></script>
HTML;
        return $html;
    }

    public function assetProd(string $entry): string
    {
        /*
            - Le cache nous permet d'optimiser les choses dans le cas ou on a plusieurs point entrées ça nous évite de lire le fichier plusieurs fois
        */
        if($this->manifestData === null) {
            $item = $this->cache->getItem(self::CACHE_KEY);
            if($item->isHit()) {
                $this->manifestData = $item->get();
            } else {
                $this->manifestData = json_decode(file_get_contents($this->manifest), true); // 'true' pour l'avoir en tableau associatif
                $item->set($this->manifestData);
                $this->cache->save($item);
            }
        }
        $file = $this->manifestData[$entry]['file'];
        $css = $this->manifestData[$entry]['css'] ?? [];
        $imports = $this->manifestData[$entry]['imports'] ?? []; // Permet de faire du préload, à vérifier dans le 'manifest.json'
        $html = <<<HTML
            <script type="module" src="/assets/{$file}" defer></script>
HTML;
        foreach($css as $cssFile) {
            $html .= <<<HTML
                <link rel="stylesheet" media="screen" href="/assets/{$cssFile}">
HTML;
        }
        foreach($imports as $import) {
            // Permet d'obtenir une meilleur note sur 'lighthouse' s'il y'a des imports
            $html += <<<HTML
HTML;
        }
        return $html;
    }
}