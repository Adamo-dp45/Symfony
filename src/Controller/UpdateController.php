<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireMethodOf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UpdateController // extends AbstractController
{
    /*
        public function __construct(
            #[AutowireMethodOf(ControllerHelper::class)]
            private \Closure $render,
            #[AutowireMethodOf(ControllerHelper::class)]
            private \Closure $redirectToRoute
        )
        {
        }

        #[Route('/home', name: 'app_home')]
        public function index(): Response
        {
            return ($this->render)('home/index.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
    */
}
