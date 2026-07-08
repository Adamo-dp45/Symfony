<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Positive;

class PaginationDTO {

    public function __construct(
        #[Positive()]
        public readonly ?int $page = 1 // readonly on ne pourra la modifier quand elle sera construite
    )
    {
    }
}