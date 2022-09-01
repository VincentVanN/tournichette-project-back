<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Utils\MySlugger;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    private $slugger;

    public static function getGroups(): array
    {
        return ['groupCart', 'groupProducts', 'devFixtures'];
    }

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [
            "Fruits",
            "Légumes",
            "Epicerie"
        ];

        foreach($categories as $currentCategory)
        {
            $categoryObj = new Category;
            $categoryObj->setName($currentCategory);
            $categoryObj->setSlug($this->slugger->slugify($currentCategory));
            $manager->persist($categoryObj);
        }

        $manager->flush();
    }
}
