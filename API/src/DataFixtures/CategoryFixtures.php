<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['groupCart'];
    }
    public function load(ObjectManager $manager): void
    {
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
