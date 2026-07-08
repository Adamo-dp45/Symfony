<?php

namespace App\Repository;

use App\Entity\FailedJob;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\ListableReceiverInterface;

class FailedJobRepository { // services.yaml

    public function __construct(
        private readonly ListableReceiverInterface $receiver
    )
    {
    }

    public function find(string $id): FailedJob
    {
        return new FailedJob($this->receiver->find($id));
    }

    /**
     * Permet de récupérer l'ensemble des messages
     */
    public function findAll(): array
    {
        // return $this->receiver->all(50); -- On peut lui donner une limite
        return array_map(fn(Envelope $envelope) => new FailedJob($envelope), iterator_to_array($this->receiver->all()));
    }

    /**
     * Permet de supprimer une tâche dans liste échouées
     */
    public function reject(string $id): void
    {
        $envelope = $this->receiver->find($id);
        if ($envelope === null) {
            throw new \InvalidArgumentException("Le message d'id $id n'existe pas");
        }
        $this->receiver->reject($envelope);
    }
}