<?php

namespace App\DataFixtures;

use App\Entity\Depot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepotFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        
        $nbDepots = 5;

        for($i = 0; $i < $nbDepots; $i++)
        {
            $depotObj = new Depot;
            $depotObj->setName($faker->unique()->company());
            $depotObj->setAddress($faker->unique()->address());
            $manager->persist($depotObj);
        }

        $manager->flush();
    }
}
