<?php

namespace App\Event;

use App\DTO\ContactDTO;

class ContactRequestEvent { // Créer manuellement

    public function __construct(public readonly ContactDTO $data)
    {
    }
}