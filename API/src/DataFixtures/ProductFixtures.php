<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $nbProducts = 5;

        // Vegetables products

        
        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Légumes"]);
        $vegetableCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $productObj->setName($faker->unique()->vegetableName());
            $productObj->setStock($faker->randomFloat(3, 1, 200));
            $productObj->setUnity('kg');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($vegetableCategory);

            $manager->persist($productObj);
        }
        
        // Fruits products

        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Fruits"]);
        $fruitCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $productObj->setName($faker->unique()->fruitName());
            $productObj->setStock($faker->randomFloat(3, 1, 200));
            $productObj->setUnity('kg');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($fruitCategory);

            $manager->persist($productObj);
        }

        // Transformed products

        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Produits transformés"]);
        $TransformedProductCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $productObj->setName($faker->randomElement([$faker->unique()->sauceName(), $faker->unique()->beverageName()]));
            $productObj->setStock($faker->randomNumber(2, false));
            $productObj->setUnity('bouteille(s)');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($TransformedProductCategory);

            $manager->persist($productObj);
        }

        $manager->flush();
    }
}
