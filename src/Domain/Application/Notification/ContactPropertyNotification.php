<?php

namespace App\Domain\Application\Notification;

use App\DTO\ContactPropertyDTO;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class ContactPropertyNotification {

    public function __construct(
        private MailerInterface $mailer,
        private Environment $renderer
    )
    {
    }

    public function notify(ContactPropertyDTO $contact)
    {
        $message = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to('bakayokoadama507@gmail.com')
            ->replyTo($contact->getEmail())
            ->subject('Agence : ' . $contact->getProperty()->getTitle())
            ->htmlTemplate($this->renderer->render('mails/contact_property.html.twig', [
                'contact' => $contact,
            ]))
        ;
        $this->mailer->send($message);
    }
}