<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $categories = [
            "Fruits",
            "Légumes",
            "Produits transformés"
        ];

        foreach($categories as $currentCategory)
        {
            $categoryObj = new Category;
            $categoryObj->setName($currentCategory);
            $manager->persist($categoryObj);
        }

        $manager->flush();
    }
}
