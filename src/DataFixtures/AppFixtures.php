<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use App\Entity\Category; // Importez la classe Category
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($j = 0; $j <= 10; $j++) {
            $category = new Category();
            $categoryName = $faker->sentence(2); // Utilisez un nom de catégorie pour chaque itération
            $category->setName($categoryName)
                     ->setSlug($this->slugger->slug($categoryName)->lower())
                     ->setDescription($faker->text(120))
                     ->setPicture($faker->imageUrl(400, 400));

            $manager->persist($category);

            for ($i = 0; $i <= 15; $i++) {
                $product = new Product();
                $productName = $faker->sentence(3);
            
                $product->setName($productName)
                        ->setDescription($faker->text(120))
                        ->setPrice($faker->randomFloat(2, 2, 2000))
                        ->setSlug($this->slugger->slug($productName)->lower())
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setPicture($faker->imageUrl(400, 400))
                        ->setCategoryId($category);
            
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
