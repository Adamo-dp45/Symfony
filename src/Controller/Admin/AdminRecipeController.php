<?php

namespace App\Controller\Admin;

use App\Domain\Helper\MarkdownHelper;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeFormType;
use App\Message\RecipePDFMessage;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route("/admin/recettes", name: 'admin.recipe.')]
#[IsGranted('ROLE_VERIFIED')] # Vérifie si l'utilisateur est vérifié
#[IsGranted('ROLE_ADMIN')]
final class AdminRecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(RecipeRepository $recipeRepository, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');
        // $em->getRepository(Recipe::class) -- Me permet aussi d'avoir son repository

        $page = $request->query->getInt('page', 1); // Page courante

        ## Voter
        /** @var User */
        $user = $this->getUser();
        $userId = $user->getId();
        $canListAll = $this->isGranted(RecipeVoter::LIST_ALL); // Vérifie s'il a le rôle LIST_ALL
        $recipes = $recipeRepository->paginateRecipes($page, $canListAll ? null : $userId); // S'il a le droit de tous lister, on met null sinon on lui passe l'id de l'utilisateur

        // $maxPage = ceil($recipes->getTotalItemCount() / 2);
        // $recipes = $recipeRepository->findWithDurationLoverThan(14);

        // Paginator Doctrine
        // $page = $request->query->getInt('page', 1);
        // $limit = 2;
        // $recipes = $recipeRepository->paginateRecipes($page, $limit);
        // $maxPages = ceil($recipes->count() / $limit);

        /* $recipes[0]->setTitle('Adama');  // Pour l'update 
        $recipe = new Recipe();
        $recipe
            ->setTitle('Barbe a papa')
            ->setSlug('barbe-papa')
            ->setDuration(10)
            ->setContent('Elle est delicieuse')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        $em->persist($recipe); // Ici le persist est le nouvel article que j'ai créer
        $em->remove($recipes[0]); // Supprime notre première recette 
        $em->flush();
        */

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            //'maxPage' => $maxPage,
            // 'page' => $page
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')] // 'RECIPE_EDIT'
    public function edit(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $em,
        UploaderHelper $helper,
        MessageBusInterface $messageBus
    ): RedirectResponse|Response
    {
        /*
            - Si on a un paramètre {id} on peut juste lui donner dans son constructeur notre entité et il fera le find tout seule
            - '$helper->asset($recipe, 'thumbnailFile')' pour avoir le chemin absolue de l'image dans les controllers via 'vich'
        */
        $form = $this->createForm(RecipeFormType::class, $recipe, [
            'is_admin' => $this->isGranted('ROLE_ADMIN') // Options
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            # Upload de symfony
            /*
                /**
                 * @var UploadedFile
                 *
                $file = $form->get('thumbnailFile')->getData();// -- Permet de récuperer les informations sur un champ en particulier, dans le cas ou on ne l'a pas mapper
                $filename = $recipe->getId() . '.' . $file->getClientOriginalExtension();
                $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images', $filename);
                $recipe->setThumbnail($filename);
            */
            # .. VichUploader, la logique d'en haut vas être gérer automatiquement
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            $em->flush();

            // -- Messenger -- //
            $messageBus->dispatch(new RecipePDFMessage($recipe->getId()), [
                new DelayStamp(1) // Permet de dire n'exécute pas cette tâche de suite mais un attent un certain délai
            ]); // En deuxième params elle peut prendre un tableau qui contiendrait des stamps, ce sont des sortes de caché sur votre message et ses caché peuvent être lu par différente transport pour éffectuer différente oppérations

            $this->addFlash('success', 'La recette a bien été modifié');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(RecipeVoter::CREATE)]
    public function create(Request $request, EntityManagerInterface $em): RedirectResponse|Response 
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe, [
            'is_admin' => $this->isGranted('ROLE_ADMIN') // Options
        ]);
        $form->handleRequest($request); // Fais le update a notre place .. set
        if($form->isSubmitted() && $form->isValid()) {
            /*
            $recipe->setCreatedAt(new \DateTimeImmutable());
            $recipe->setUpdatedAt(new \DateTimeImmutable());
            */
            $recipe->setUser($this->getUser());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien été crée');
            return $this->redirectToRoute('admin.recipe.index');
        }

        /* Turbo
        if ($form->isSubmitted() && !$form->isValid()) {
            -- Turbo s'attend à un code 422 pour afficher la réponse dans le stream
            return $this->render('report/new.html.twig', [
                'form' => $form->createView(),
            ], new Response('', 422));
        }
        */

        return $this->render('admin/recipe/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: 'delete', methods:['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function remove(Request $request, Recipe $recipe, EntityManagerInterface $em)
    {
        $recipeId = $recipe->getId();
        $em->remove($recipe);
        $em->flush();

        $message = 'La recette a bien été supprimé';
        if($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('admin/recipe/_delete.html.twig', ['recipeId' => $recipeId, 'message' => $message]);
        }

        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin.recipe.index');
    }

}
