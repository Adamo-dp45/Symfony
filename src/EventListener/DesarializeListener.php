<?php

namespace App\EventListener;

use ApiPlatform\State\SerializerContextBuilderInterface;
use ApiPlatform\State\Util\RequestAttributesExtractor;
use ApiPlatform\Symfony\EventListener\DeserializeListener as DecoratedListner;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DesarializeListener // service.yaml
{
    public function __construct(
        private DecoratedListner $decorated,
        private SerializerContextBuilderInterface $serializer, /*
            - Permet de générer un context à partir d'un requête
        */
        private DenormalizerInterface $denormalizer
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if($request->isMethodCacheable() || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }
        if($request->getContentTypeFormat() === 'form') { /*
            - Si la requête est de type formulaire 'multipart' on fais un traîtement sinon on le retourne comme il était
        */
            $this->denormalizeFromRequest($request);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeFromRequest(Request $request): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        if(empty($attributes)) {
            return;
        }
        $context = $this->serializer->createFromRequest($request, false, $attributes); // 'false' on n'a pas l'étape de la normalisation
        $populated = $request->attributes->get('data');
        if($populated !== null) {
            $context['object_to_populate'] = $populated; /*
                - Pour avoir l'objet qui a été récupérer depuis la base de données comme ça quand il va dénormalizer les choses il va prendre cet objet comme base et il rajoutera dans à l'intérieur de l'objet les informations qui sont passé dans la requête
            */
        }
        $data = $request->request->all();
        $files = $request->files->all();
        $object = $this->denormalizer->denormalize(
            array_merge($data, $files),
            $attributes['resource_class'],
            null,
            $context
        );
        $request->attributes->set('data', $object); /*
            - Pour qu'il écrire le fichier on a préciser le groupe de dénormalization dans 'put:Post' sur 'imageFile' dans 'Post' 
        */
    }
}