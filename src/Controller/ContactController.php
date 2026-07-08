<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\ContactMessage;
use App\Form\ContactFormType;
use App\Form\ContactMessageFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use App\Event\ContactRequestEvent;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(
        Request $request,
        MailerInterface $mailer,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ): Response
    {
        $data = new ContactDTO();
        $form = $this->createForm(ContactFormType::class, $data);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre email a bien été envoyé');
                return $this->redirectToRoute('contact');
            } catch(\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyé votre email' . $e->getMessage());
            }
            /*
            try {
                $email = (new TemplatedEmail()) // Aussi il y'a new Email()
                    ->to($data->service)
                    ->from($data->email)
                    ->subject('Demande de contact')
                    ->htmlTemplate('mails/contact.html.twig')
                    ->context(['data' => $data]);
                    -- ->locale('de') Change le locale
                $mailer->send($email);
                $this->addFlash('success', 'Votre email abien été envoyé');
                return $this->redirectToRoute('contact');
            } catch(\Exception $e) {
                $this->addFlash('danger', 'Impossible d\'envoyé votre email' . $e->getMessage());
            }
            */
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/contact/message', name: 'contact.message')]
    public function message(
        Request $request,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ): Response
    {
        $ip = $request->getClientIp();
        $today = new \DateTimeImmutable('today');
        $contact = new ContactMessage();
        $form = $this->createForm(ContactMessageFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repo = $em->getRepository(ContactMessage::class);
            $existing = $repo->createQueryBuilder('c')
                ->where('c.ip = :ip')
                ->andWhere('c.createdAt >= :today')
                ->setParameter('ip', $ip)
                ->setParameter('today', $today)
                ->getQuery()
                ->getOneOrNullResult();

            if ($existing) {
                $this->addFlash('error', 'Vous avez déjà envoyé un message aujourd\'hui.');
            } else {

                // Envoyer Email

                $contact->setCreatedAt(new \DateTimeImmutable());
                $contact->setIp($ip);
                $em->persist($contact);
                $em->flush();
                $this->addFlash('success', 'Votre message a été envoyé.');
                return $this->redirectToRoute('contact');
            }
        }

        return $this->render('contact/message.html.twig', [
            'form' => $form
        ]);
    }

}
