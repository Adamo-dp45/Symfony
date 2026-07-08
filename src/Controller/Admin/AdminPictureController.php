<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class AdminPictureController extends AbstractController
{
    #[Route('/admin/picture/{id}', name: 'admin.picture.delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(Picture $picture, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        if($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
            $em->remove($picture);
            $em->flush();
            // $this->addFlash('success', 'Image supprimé avec succès');
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => ['Token invalid'], 400]);
        }
        // return $this->redirectToRoute('admin.property.edit', ['id' => $picture->getProperty()->getId()]);
    }

}