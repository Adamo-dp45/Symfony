<?php

namespace App\Controller;

use App\Domain\Application\Notification\ContactPropertyNotification;
use App\DTO\ContactPropertyDTO;
use App\DTO\PropertySearch;
use App\Entity\Property;
use App\Form\ContactPropertyFormType;
use App\Form\PropertySearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PropertyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Requirement\Requirement;

final class PropertyController extends AbstractController
{
    #[Route('/biens', name: 'property.index')]
    public function index(PropertyRepository $property, Request $request): Response
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('home')],
            ['label' => 'Produits', 'url' => null],
        ];

        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchFormType::class, $search);
        $form->handleRequest($request);
        $properties = $property->findAllVisibleQuery($search);
        return $this->render('property/index.html.twig', [
            'property' => $properties,
            'form' => $form,
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    #[Route('/biens/{slug}/{id}', name: 'property.show', requirements: ['id' => Requirement::DIGITS, 'slug' => Requirement::ASCII_SLUG])]
    public function show($slug, $id, Property $property, PropertyRepository $repository, Request $request, ContactPropertyNotification $notification, ParameterBagInterface $params): Response
    {
        $contact = new ContactPropertyDTO();
        $contact->setProperty($property);
        $form = $this->createForm(ContactPropertyFormType::class, $contact);
        $form->handleRequest($request);
        $key = $params->get('recaptcha_key');

        if($form->isSubmitted() && $form->isValid()) {
            // $notification->notify($contact);
            $this->addFlash('success', 'Votre email à été envoyé');
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ]);
        }

        if($property->getSlug() !== $slug) {
            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
            ], 301); // En troisième paramètre on peut lui donner le status
        }
        $pro = $repository->find($id);
        return $this->render('property/show.html.twig', [
            'property' => $pro,
            'form' => $form,
            'key' => $key
        ]);
    }

}
