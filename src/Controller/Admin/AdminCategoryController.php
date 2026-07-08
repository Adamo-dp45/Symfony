<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route("/admin/category", name: 'admin.category.')]
#[IsGranted('ROLE_ADMIN')]
final class AdminCategoryController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(CategoryRepository $repository): Response 
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repository->findAllWithCount()
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em) 
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La categorie a bien été créer');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form 
        ]);
    }

    #[Route('/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $em) 
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La categorie a bien été modifié');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form 
        ]);
    }

    #[Route('/corbeille', name: 'corbeille')]
    public function corbeille(CategoryRepository $repository): Response 
    {
        $categories = $repository->findByDelete();

        return $this->render('admin/trash/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'La categorie a bien été supprimée');
        return $this->redirectToRoute('admin.category.index');
    }

    #[Route('/trash/{id}', name: 'trash', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function trash(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        // if ($this->isCsrfTokenValid('delete_' . $category->getId(), $request->request->get('_token'))) {
            $category->setDeleteAt(new \DateTimeImmutable());
            $em->flush();
            $this->addFlash('warning', 'La catégorie a été déplacé dans la corbeille');
        // }
        return $this->redirectToRoute('admin.category.index');
    }

    #[Route('/restore/{id}', name: 'restore', methods: ['POST'])]
    public function restore(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('restore_' . $category->getId(), $request->request->get('_token'))) {
            $category->setDeleteAt(null);
            $em->flush();
            $this->addFlash('success', 'Le catégorie a été restauré');
        }
        return $this->redirectToRoute('admin.category.index');
    }

    public function getTrashCount(CategoryRepository $categoryRepository): int
    {
        return $categoryRepository->count(['deleteAt' => ['not' => null]]); // TwigExtension pour l'affichage
    }

    /*
        <li class="nav-item">
            <a class="nav-link text-danger" href="{{ path('admin_reports_trash') }}">
                <i class="bi bi-trash3"></i>
                Corbeille des Rapports
                {% if report_trash_count() > 0 %}
                    <span class="badge bg-danger">{{ report_trash_count() }}</span>
                {% endif %}
            </a>
        </li>
    */
}
