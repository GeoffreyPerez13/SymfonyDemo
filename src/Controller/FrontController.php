<?php

namespace App\Controller;

use App\Entity\Category;
use Twig\Environment;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $date = new \DateTime();

        $products = $productRepository->findAll();

        return $this->render('front/index.html.twig', [
            'date' => $date,
            'products' => $products,
        ]);
    }

    //#[Route('/product/{id<\d+>?}', name: 'app_product_detail')]
    #[Route('/product/{slug}', name: 'app_product_detail')]
    public function productDetail(Product $product = null): Response
    {
        if ($product === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/pages/productDetail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/categories', name: 'app_categories')]
    public function categories(CategoryRepository $categoryRepository): Response
    {

        $categories = $categoryRepository->findAll();

        return $this->render('front/categories.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{slug}', name: 'app_category_detail')]
    public function categoryDetail(Category $category = null): Response
    {
        if ($category === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/pages/categoryDetail.html.twig', [
            'category' => $category,
        ]);
    }

    /*#[Route('/product/{id<\d+>?}', name: 'app_product_detail')]
    public function productDetail(Product $product = null): Response
    {
        if ($product === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/pages/product_detail.html.twig', [
            'product' => $product,
        ]);

        // Bouton a placer dans l'index
        <a class="btn btn-primary" href="{{ path('app_product_detail', {id: product.id}) }}">Voir les détails</a>

    }*/

    #[Route('/pages/{page}', name: 'app_static_page', requirements: ['page' => '[a-z]+'])]
    public function staticPage(string $page, Environment $twig): Response
    {
        $template = 'front/pages/' . $page . '.html.twig';
        $loader = $twig->getLoader();
        if (!$loader->exists($template)) {
            throw new NotFoundHttpException();
        }

        return $this->render($template, []);
    }
}
