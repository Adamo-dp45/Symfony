<?php

namespace App\Controller\Admin;

use App\Entity\Option;
use App\Form\OptionFormType;
use App\Repository\OptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/option')]
final class AdminOptionController extends AbstractController
{
    #[Route(name: 'app_admin_option_index', methods: ['GET'])]
    public function index(OptionRepository $optionRepository): Response
    {
        return $this->render('admin/option/index.html.twig', [
            'options' => $optionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_option_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionFormType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($option);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/option/new.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_option_show', methods: ['GET'])]
    public function show(Option $option): Response
    {
        return $this->render('admin/option/show.html.twig', [
            'option' => $option,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_option_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OptionFormType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_option_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/option/edit.html.twig', [
            'option' => $option,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($option);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_option_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/options', name: "api.option")]
    public function search(Request $request, OptionRepository $optionRepository)
    {
        return $this->json($optionRepository->search($request->query->get('q')), 200, [], [
            "groups"=> ['option.index']
        ]);
    }
}
