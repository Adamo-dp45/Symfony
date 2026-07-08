<?php

namespace App\Controller\Api;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class MeController {

    public function __construct(
        private Security $security
    )
    {
    }

    public function __invoke(): UserInterface|null
    {
        $user = $this->security->getUser();
        return $user;
    }
}