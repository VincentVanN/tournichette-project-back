<?php

namespace App\DataFixtures;

use App\Entity\Depot;
use App\Utils\MySlugger;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class DepotFixtures extends Fixture implements FixtureGroupInterface
{
    private $slugger;

    public static function getGroups(): array
    {
        return ['groupDepot'];
    }

    public function __construct(MySlugger $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $fakerFr = \Faker\Factory::create('fr_FR');
        
        $nbDepots = 5;

        for($i = 0; $i < $nbDepots; $i++)
        {
            $depotObj = new Depot;
            $depotObj->setFirstname($faker->firstName());
            $depotObj->setLastname($faker->lastName());
            $depotObj->setSlug($this->slugger->slugify($depotObj->getFirstname() . ' ' . $depotObj->getLastname()));
            $depotObj->setAddress($faker->unique()->address());

            $phoneDepot = $fakerFr->unique()->serviceNumber();
            $phoneNoSpaceDepot = str_replace(' ', '', $phoneDepot);
            $depotObj->setPhone($phoneNoSpaceDepot);

            $manager->persist($depotObj);
        }

        $manager->flush();
    }
}