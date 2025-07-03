<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Product;


final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $query = $request->query->get('q');

        $categoryId = $request->query->get('category');

        $categories = $categoryRepository->findAll();

        $products = $productRepository->findBySearchAndCategory($query, $categoryId);

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    #[Route('/user/product/{id}', name: 'user_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('home/show.html.twig', [
            'product' => $product
        ]);
    }

}
