<?php

namespace App\DTO;

class CategoryWithCountDTO {

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $count,
    ) {
    } // readonly vue qu'elle ne seront initialer qu'a la construction et qu'elle ne seront jamais modifier
}