<?php

namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController {

    /*
        - On vas mettre en place notre système d'authentification qui va se déclanché dès qu'il va voir dans les entêtes un clé 
            - Authorization : Bearer 'apiKey'

        - On peut utiliser les groupes serialization pour ne pas exposer le mot de passe
    */
    #[Route('/api/me', name: "api.me")]
    #[IsGranted('ROLE_USER')]
    public function me(): JsonResponse
    {
        /**
         * @var User
         */
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUsername(),
            'email' => $user->getEmail()
        ]);
    }

}