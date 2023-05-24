<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Cocur\Slugify\Slugify;
use App\Repository\CategoryRepository;



class AdminProductController extends AbstractController
{
    #[Route('/admin/product', name: 'app_admin_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }
    
    #[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $product = new Product();

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('picture', FileType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'query_builder' => fn(CategoryRepository $repository) =>
                    $repository->createQueryBuilder('c')->orderBy('c.name', 'ASC')
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setCreatedAt(new \DateTimeImmutable());
            
            $category = $form->get('category')->getData();
            $product->setCategory($category);

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