<?php

namespace App\DataFixtures;

use App\Entity\SalesStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class SalesStatusFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['devFixtures', 'prodFixtures'];
    }

    public function load(ObjectManager $manager): void
    {
        $salesStatus = new SalesStatus;
        $salesStatus->setName('status');
        $salesStatus->setEnable(true);

        $manager->persist($salesStatus);
        $manager->flush();
    }
}
