<?php

namespace App\Controller;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use Cocur\Slugify\Slugify;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class AdminProductController extends AbstractController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }
    
    #[Route('/admin/product', name: 'app_admin_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    #[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $faker = Factory::create();
        
        $category = new Category();
        $categoryName = $faker->sentence(2);
        $category->setName($categoryName)
                ->setSlug($this->slugger->slug($categoryName)->lower())
                ->setDescription($faker->text(120))
                ->setPicture($faker->imageUrl(400, 400));
        
        $product = new Product();
        
        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug à partir du nom du produit
            $slugify = new Slugify();
            $slug = $slugify->slugify($product->getName());
            
            // Assigner le slug au champ 'slug' de l'entité Product
            $product->setSlug($slug)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setCategoryId($category);
            
            $entityManager->persist($category);
            $entityManager->persist($product);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_admin_product');
        }
        
        return $this->render('admin_product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*#[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_product');
        }

        return $this->render('admin_product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }*/
}