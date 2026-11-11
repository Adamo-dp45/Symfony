<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use App\Form\PropertyFormType;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

final class AdminPropertyController extends AbstractController {

    public function __construct(private PropertyRepository $propertyRepository)
    {
    }

    #[Route('/admin/property', name: 'admin.property.index')]
    public function index() 
    {
        $properties = $this->propertyRepository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }

    #[Route('/admin/property/edit/{id}', name: 'admin.property.edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Property $property, Request $request, EntityManagerInterface $em, CacheManager $cacheManager, UploaderHelper $helper): Response
    {
        $form = $this->createForm(PropertyFormType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            /* - Liip imagine
                if($property->getImageFile() instanceof UploadedFile) { -- Si on n'a une instance de 'UploadedFile' au lieu de 'File' ça signifie qu'on n'a un fichier qui a été uplodé
                    $cacheManager->remove($helper->asset($property, 'imageFile')); -- Permet de supprimer le cache d'un fichier
                } -- On peut le faire de cette manière mais aussi avec les évènements doctrine ImageCacheSubscriber
            */

            $em->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig', [
            'property' => $property,
            'form' => $form
        ]);
    }

    #[Route('/admin/bien/create', name: 'admin.property.new')]
    public function new(Request $request, EntityManagerInterface $em): RedirectResponse|Response
    {
        $property = new Property();
        $form = $this->createForm(PropertyFormType::class, $property);
        $form->handleRequest($request); // Traite la requête
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($property);
            $em->flush();
            $this->addFlash('success', 'Bien créer avec succès');
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/new.html.twig', [
            'property' => $property,
            'form' => $form
        ]);
    }

    #[Route('/admin/{id}', name: 'admin.property.delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Property $property, Request $request, EntityManagerInterface $em)
    {
        if($this->isCsrfTokenValid('delete' . $property->getId(), $request->getPayload()->getString('_token'))) {
            $em->persist($property);
            $em->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('admin.property.index');
    }

}