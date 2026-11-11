<?php

namespace App\Controller;

use App\DTO\ProductSearch;
use App\Form\ProductSearchFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ProductController extends AbstractController
{
    #[Route('/produits', name: 'product')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $data = new ProductSearch();
        $data->page = $request->query->getInt('page', 1);
        $form = $this->createForm(ProductSearchFormType::class, $data);
        $form->handleRequest($request);
        [$min, $max] = $productRepository->findMinMax($data);
        $products = $productRepository->findSearch($data);

        // if($request->isXmlHttpRequest()) { -- Si on n'est en Ajax
        if($request->query->get('ajax')) { // Pour éviter le retour json en affichage dans mon cas
            return new JsonResponse([
                'content' => $this->renderView('product/_products.html.twig', ['products' => $products]),
                'sorting' => $this->renderView('product/_sorting.html.twig', ['products' => $products]),
                'paginnation' => $this->renderView('product/_paginnation.html.twig', ['products' => $products]),
                'pages' => ceil($products->getTotalItemCount() / $products->getItemNumberPerPage()),
                'min' => $min,
                'max' => $max
            ]);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form,
            'min' => $min,
            'max' => $max
        ]);
    }
}
