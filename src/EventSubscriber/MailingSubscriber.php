<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;
        $email = (new TemplatedEmail())
            ->to($data->service)
            ->from($data->email)
            ->subject('Demande de contact')
            ->htmlTemplate('mails/contact.html.twig')
            ->context(['data' => $data]);
        $this->mailer->send($email);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if(!$user instanceof User) {
            return;
        }
        $email = (new Email()) // En format text
            ->to($user->getEmail())
            ->from('support@demo.fr')
            ->subject('Connexion')
            ->text('Vous vous êtes connecter');
        $this->mailer->send($email);
        // Quelques choses de pertinant est lors de l'inscription
    }

    /**
     * Listes l'ensemble des évènements au quel on s'abonne
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            // InteractiveLoginEvent::class => 'onLogin' -- L'évènement lorsqu'un utilisateur se connecte grâce au formulaire
        ];
    }
}
