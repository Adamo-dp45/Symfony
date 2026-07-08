<?php

namespace App\Controller;

use App\Domain\Helper\MarkdownHelper;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recipe', name: 'app_recipe')]
    public function index(EntityManagerInterface $em): Response
    {
        /*
            $recipes = $this->entityManager->getRepository(Recipe::class)->findBy([
                'createdAt' => ['<', $archiveDate],
                'archivedAt' => null
            ]);
        */
        return $this->render('recipe/index.html.twig', [
            'controller_name' => 'RecipeController',
        ]);
    }

    #[Route('/recipe/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $recipeRepository, MarkdownHelper $markdown): Response
    {
        // $request->attributes->get('slug') -- Permet aussi de récuéprer les paramètre de la requête
        // $request->attributes->getInt('id') -- Va nous le donner en entier
        $recipe = $recipeRepository->find($id);
        if($recipe->getSlug() !== $slug) {
            $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }
        return $this->render('recipe/show.html.twig', [
            'slug' => $slug,
            'id' => $id,
            'recipe' => $recipe,
            'person' => [
                'firstname' => 'John',
                'lastname' => 'Doe'
            ],
            'html' => '<strong>Moi</strong>',
            'content' => $markdown->parse($recipe->getContent())
        ]);
    }

}