<?php

namespace App\Controller\Api;

use App\DTO\PaginationDTO;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use App\ValueResolver\Attribute\MapHydratedEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecipeController extends AbstractController {

    #[Route("/api/recipes", methods: ["GET"])] // PaginationNormalizer - Va modifier la normalisation de nos données 
    // #[IsGranted('ROLE_USER')] -- Méthode start dans AppAuthenticator
    public function index(
        RecipeRepository $recipeRepository,
        Request $request,
        SerializerInterface $serializer,
        #[MapQueryString()] // Automatiquement il va prendre tous les paramètre qui sont dans la queryString et il va éssayer de la désérialiser dans ce cas il va me créer mon objet en mettant la valeur de page à ce qui est dans l'url (Ex: -3), Vu que j'ai mis une règle de validation sur mon DTO, ça va poser une erreur
        PaginationDTO $paginationDTO // Pour un problème de version
    ): JsonResponse
    {
        $recipes = $recipeRepository->paginateRecipes($paginationDTO->page); // Le ? signifie que je dois utiliser la méthode optionel
        // Dans le paginateRecipes je m'attend à obtenir le numéro de page, dans ce cas l'utilisateur peut mettre un nombre négative ce qui va causer une erreur donc je vais utiliser un DTO

        // $recipes = $recipeRepository->paginateRecipes($request->query->getInt("page", 1)); // Ici je vais utiliser un normalizer personaliser pour la partie pagination - PaginationNormalizer

        // dd($serializer->serialize($recipes,"csv", [ // xml yaml
        //    "groups"=> ['recipes.index']
        // ]));

        return $this->json($recipes, 200, [], [
            "groups"=> ['recipes.index'] # Permet de spécifier la listes des champs qu'on veut convertir et ensuite expliquer au niveau de notre entité quel champ on veut convertir, groupe de normalisation
        ]);
    }

    #[Route("/api/recipes/{id}", requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            "groups"=> ['recipes.index', 'recipes.show']
        ]);
    }

    #[Route("/api/recipes", methods: ["POST"])]
    public function new(
        Request $request,
        #[MapRequestPayload( // Permet de dire que le payload de la requête (ici notre json) va être injecter dans cet objet, prend le contenu de la requête et l'injecte dans notre objet, il va utiliser un système deserialzation pour convertir le json recu sous forme tableau php et va utiliser la denormalization pour convertir ce tableau php en objet, s'il y'a une erreur d'assertion il renvera les erreurs sous forme de json, il gère aussi du xml
            serializationContext: [
                "groups"=> ['recipes.new']
            ],
            validationGroups: ['Extra'] // Permet de définir les groupes de validations à utiliser
        )] // Par défaut qu'en on vas utiliser le MapRequestPayload il va mettre en plus le processus de validation
        Recipe $recipe,
        // SerializerInterface $serializer
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse
    {
        // $recipe = new Recipe();
        $recipe->setCreatedAt(new DateTimeImmutable())
            // ->setUser(new User())
            ->setUpdatedAt(new DateTimeImmutable());

        $violations = $validator->validate($recipe);
        if($violations->count() > 0) { // Pour le PUT et #[MapEntity()] sur l'entité est celui qui utiliser de manière implicite lorsqu'une entité doit être hydraté
            return $this->json($violations, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // $em->persist($recipe);
        // $em->flush();
        // return $this->json($recipe, Response::HTTP_CREATED);

        return $this->json($recipe, 200, [], [
            "groups"=> ['recipes.index', 'recipes.show']
        ]);

        // Sol 1
        // dd($serializer->deserialize($request->getContent(), Recipe::class, "json", [
        //    AbstractNormalizer::OBJECT_TO_POPULATE => $recipe, // Au lieu de créer une nouvelle entité, il utilise celui la
        //    "groups"=> ['recipes.new']
        // ])); -- Voilà comment on traiterai un requête rentrante et ensuite il faudra valider les données avant de les enregister en bdd
    }

    #[Route("/api/recipes/{id}", requirements: ['id' => Requirement::DIGITS], methods: ['PATCH'])]
    public function update(
        // #[MapEntity()]
        #[MapHydratedEntity(groups: ['recipes.update'])]
        Recipe $recipe,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        Request $request
    ): JsonResponse
    {
        // $data = json_decode($request->getContent(), true);

        /* -- Si on veut utiliser notre MapHydratedEntity
        $recipe = $serializer->deserialize(
            $request->getContent(),
            $recipe::class,
            $request->getContentTypeFormat(),
            ['groups' => 'recipes.update', AbstractNormalizer::OBJECT_TO_POPULATE => $recipe]
        );

        $violations = $validator->validate($recipe, null, groups: ['Extra']);
        if($violations->count() > 0) {
            return $this->json($violations, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        */

        $em->flush();
        return $this->json($recipe, 200, [], [
            "groups"=> ['recipes.index', 'recipes.show']
        ]);
    }
}