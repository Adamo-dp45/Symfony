<?php

namespace App\Domain\Application\Service;

use Twilio\Rest\Client;

class TwilioService {

    // private Client $client;
    private $client;

    private string $from;

    public function __construct(string $sid, string $token, string $from)
    {
        // $this->client = new Client($sid, $token);
        $this->from = $from;
    }

    public function sendSms(string $to, string $message): void
    {
        $this->client->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message
            ]
        );
    }

}