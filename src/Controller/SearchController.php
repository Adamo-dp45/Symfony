<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SearchController extends AbstractController
{
    #[Route('/api/search', name: 'api.search')]
    public function index(Request $request, UserRepository $userRepository, CategoryRepository $categoryRepository): Response
    {
        $q = $request->query->get('q', null);

        if ($request->isXmlHttpRequest() && !$q) {
            return $this->json(['error' => 'Erreur']);
        } elseif(!$q) {
            return $this->render('search/index.html.twig'); // Redirection ou lui envoyer les variables
        }

        $category = $categoryRepository->searchBy($q);
        $users = $userRepository->searchBy($q);

        if($request->isXmlHttpRequest()) {
            return $this->json([
                'query' => $q,
                'category' => $category,
                'users' => $users
            ], 200, [], [
                "groups"=> ['users']
            ]);
        } else {
            return $this->render('search/index.html.twig', [
                'query' => $q,
                'category' => $category,
                'users' => $users,
            ]);
        }
    }
}