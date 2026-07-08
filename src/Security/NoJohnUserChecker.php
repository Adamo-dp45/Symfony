<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NoJohnUserChecker implements UserCheckerInterface // Dans 'security.yaml' - 'user_checker'
{
    /**
     * Permet de faire un traitement avant l'authentification
     * @param UserInterface $user
     * @return void
     */
    public function checkPreAuth(UserInterface $user): void
    {
        $this->checkAuth($user);
    }

    /**
     * Permet de faire un traitement après l'authentification
     * @param UserInterface $user
     * @return void
     */
    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkAuth($user);
    }

    private function checkAuth(UserInterface $user)
    {
        if(str_contains($user->getUserIdentifier(), 'user1')) {
            throw new CustomUserMessageAccountStatusException('Pas de user1 sur le site'); /*
                - L'exception.. permet d'indiquer des erreurs d'authentification
            */
        }
        // Ou --
        /*
            if(!$user instanceof User) {
                return;
            }
            str_contains($user->getEmail(), 'user1@gmail.com')
        */
    }
}
