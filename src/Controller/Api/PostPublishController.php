<?php

namespace App\Controller\Api;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostPublishController extends AbstractController {

    public function __invoke(Post $post): Post
    {
        $post->setOnline(true);
        return $post;
    }
}