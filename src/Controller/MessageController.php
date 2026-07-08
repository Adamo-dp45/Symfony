<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class MessageController extends AbstractController
{
    #[Route('/message', name: 'message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/send', name: 'message.send')]
    public function send(Request $request, EntityManagerInterface $em): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $message
                ->setCreatedAt(new \DateTimeImmutable())
                ->setSender($this->getUser());
            $em->persist($message);
            $em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé');
            return $this->redirectToRoute('message');
        }

        return $this->render('message/send.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/recu', name: 'message.recieved')]
    public function recieved(): Response
    {
        return $this->render('message/recieved.html.twig', []);
    }

    #[Route('/sent', name: 'message.sent')]
    public function sent(): Response
    {
        return $this->render('message/sent.html.twig', []);
    }

    #[Route('/lu/{id}', name: 'message.read')]
    public function read(Message $message, EntityManagerInterface $em): Response
    {
        if(!$message->isRead()) {
            $message->setIsRead(true);
            // $em->persist($message);
            $em->flush();
        }
        return $this->render('message/read.html.twig', [
            'message' => $message
        ]);
    }

    #[Route('/supprimer/{id}', name: 'message.delete')]
    public function delete(Message $message, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($message);
        $em->flush();
        return $this->redirectToRoute('message.recieved');
    }

    // Repondre à un message
    // Corbeille
    // On peut gérer la suppression des deux côté avec les boolean, tant que les 2 n'ont pas supprimé on ne le fais pas définitivement

    /*
        `Message` - Messages utilisateurs
            > sender - ManyToOne - User - sent : Expéditeur
                > User - sent : Méssages envoyés
            > recipient - ManyToOne - User : Destinataire
                > User - received : Méssages reçu
    */
}
