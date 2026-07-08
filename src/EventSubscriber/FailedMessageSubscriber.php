<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Mime\Email;

class FailedMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly MailerInterface $mailer
    )
    {
    }
    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event): void
    {
        // $event->getEnvelope(); -- Récupère le contenu du message échoué

        $message = get_class($event->getEnvelope()->getMessage());
        $trace = $event->getThrowable()->getTraceAsString();
        $email = (new Email())
            ->from('noreply@server.fr')
            ->to('adamo.dp45@gmail.com')
            ->text(<<<TEXT
                Une erreur est survenue lors du traitement d'une tâche

                {$message}

                {$trace}
TEXT
            );
        $this->mailer->send($email);
    }
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
        ];
    }
}
