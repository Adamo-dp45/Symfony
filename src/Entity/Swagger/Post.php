<?php

namespace App\Entity\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
class Post {

    /**
     * @OA\Property(type="integer")
     * @var int
     */
    public $id;

    /**
     * @OA\Property(type="string", nullable=true)
     * @var string|null
     */
    public $name;

    /**
     * @OA\Property(type="string", nullable=true)
     * @var string|null
     */
    public $content;

    /**
     * @OA\Property(type="string", format="date-time", nullable=true)
     * @var \DateTimeInterface|null
     */
    public $createdAt;

}