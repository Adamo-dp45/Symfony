<?php

namespace App\Controller;

use App\Infrastructure\Image\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ImageController extends AbstractController
{
    #[Route('/images/{path}', name: 'glide', methods: ['GET'], requirements: ['path' => '.+'])]
    public function glide(
        Request $request,
        string $path,
        /* Signature $signature */
        ParameterBagInterface $params
    )
    {
        $server = ServerFactory::create([
            'response' => new SymfonyResponseFactory(),
            'source' => $params->get('kernel.project_dir') . '/public/images/recipes',
            'cache' => $params->get('kernel.cache_dir') . '/public/images/cache', // Cache
            'base_url' => '/images', // Préfixe pour les routes glide
            // 'cache_path_prefix' => '.cache', -- On peut s'en passer si la source et le cache ne sont pas au même niveau
            // 'driver' => 'imagick' -- Changer le driver
            // 'source_path_prefix' => '/images', -- Permet d'ajouter ce prefix avant le chemin source
            // 'sign_key' => $_ENV['GLIDE_KEY'], -- Sécurité
        ]);

        /*
            -- Pour limiter les paramètres autorisés
            $allowed = ['w', 'h', 'fit', 'crop'];
            $params = array_intersect_key($request->query->all(), array_flip($allowed));
        */
        /*
            [$url] = explode('?', $request->getRequestUri()); -- 'getRequestUri' renvoie le chemin plus le query string
            try {
                SignatureFactory::create($params->get('app.glide.key'))->validateRequest($url, ['s' => $request->get('s')]); // Le 's' vient de la signature
                return $server->getImageResponse($path, $request->query->all()); 
            } catch (SignatureException) {
                throw new HttpException(403, 'Signature invalide');
            }
            $server->deleteCache($path); -- Supprimer le cache
            $request->getPathInfo(); -- Url
        */
        return $server->getImageResponse($path, $request->query->all());
    }
}
