<?php

namespace App\Controller\Admin;

use App\Repository\FailedJobRepository;
use App\Repository\LoginHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN', statusCode: 403)] // #[IsGranted('ROLE_ADMIN', statusCode: 403, exceptionCode: 10010)]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(
        LoginHistoryRepository $loginHistoryRepository,
        FailedJobRepository $failedJobRepository
    ): Response
    {
        $logins = $loginHistoryRepository->findBy([], ['loginAt' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'logins' => $logins,
            'jobs' => $failedJobRepository->findAll()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, FailedJobRepository $failedJobRepository): RedirectResponse
    {
        $failedJobRepository->reject($id);
        $this->addFlash('success', 'La tâche a bien été supprimée');
        return $this->redirectToRoute('home');
    }

    #[Route('/retry/{id}', name: 'delete', methods: ['POST'])]
    public function retry(int $id, FailedJobRepository $failedJobRepository, MessageBusInterface $bus): RedirectResponse
    {
        $message = $failedJobRepository->find($id)->getMessage();
        $bus->dispatch($message);
        $failedJobRepository->reject($id);
        $this->addFlash('success', 'La tâche a bien été réesayée');
        return $this->redirectToRoute('home');
    }

}
