<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentCreateController extends AbstractController {

    public function __invoke(Comment $data): Comment
    {
        $data->setAuthor($this->getUser());
        return $data;
    }
}