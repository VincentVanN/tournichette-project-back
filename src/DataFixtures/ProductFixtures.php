<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Utils\MySlugger;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{

    private $slugger;

    public static function getGroups(): array
    {
        return ['groupCart', 'groupProducts', 'devFixtures'];
    }
    
    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $nbProducts = 5;

        // Vegetables products

        
        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Légumes"]);
        $vegetableCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $nameProduct = $faker->unique()->vegetableName();
            $productObj->setName($nameProduct);
            $productObj->setSlug($this->slugger->slugify($nameProduct));
            $productObj->setStock($faker->randomFloat(3, 1, 200));
            $productObj->setUnity('kg');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($vegetableCategory);
            $productObj->setDescription($faker->text());
            $productObj->setColorimetry($faker->randomElement(['cold', 'hot']));
            $productObj->setArchived(false);

            $manager->persist($productObj);
        }
        
        // Fruits products

        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Fruits"]);
        $fruitCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $nameProduct = $faker->unique()->fruitName();
            $productObj->setName($nameProduct);
            $productObj->setSlug($this->slugger->slugify($nameProduct));
            $productObj->setStock($faker->randomFloat(3, 1, 200));
            $productObj->setUnity('kg');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($fruitCategory);
            $productObj->setDescription($faker->text());
            $productObj->setColorimetry($faker->randomElement(['cold', 'hot']));
            $productObj->setArchived(false);

            $manager->persist($productObj);
        }

        // Grocery products

        $categoryObj = $manager->getRepository(Category::class)->findBy(["name" => "Epicerie"]);
        $TransformedProductCategory = $categoryObj['0'];

        for ($i = 0; $i < $nbProducts; $i++)
        {
            $productObj = new Product;
            $nameProduct = $faker->randomElement([$faker->unique()->sauceName(), $faker->unique()->beverageName()]);
            $productObj->setName($nameProduct);
            $productObj->setSlug($this->slugger->slugify($nameProduct));
            $productObj->setStock($faker->randomNumber(2, false));
            $productObj->setUnity('bouteille(s)');
            $productObj->setPrice($faker->randomFloat(2, 1, 10));
            $productObj->setCategory($TransformedProductCategory);
            $productObj->setDescription($faker->text());
            $productObj->setColorimetry($faker->randomElement(['cold', 'hot']));
            $productObj->setArchived(false);

            $manager->persist($productObj);
        }

        $manager->flush();
    }
}
