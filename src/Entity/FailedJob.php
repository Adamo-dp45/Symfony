<?php

namespace App\Entity;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;
use Symfony\Component\Messenger\Stamp\TransportMessageIdStamp;

class FailedJob {

    public function __construct(
        private Envelope $envelope
    )
    {
    }

    public function getId()
    {
        /**
         * @var TransportMessageIdStamp[] $stamps
         */
        $stamps = $this->envelope->all(TransportMessageIdStamp::class);
        return end($stamps)->getId();
    }

    public function getTitle(): string
    {
        return get_class($this->envelope->getMessage());
    }

    public function getTrace(): string
    {
        /**
         * @var RedeliveryStamp[] $stamps
         */
        $stamps = $this->envelope->all(RedeliveryStamp::class);
        // !! //
        return '';
        return $stamps[0]->getFlattenException()->getTraceAsString();
    }

    public function getMessage(): object
    {
        return $this->envelope->getMessage();
    }
}