<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        $superAdminRole = new Role;
        $superAdminRole->setName('ROLE_SUPER_ADMIN');
        $manager->persist($superAdminRole);

        $adminRole = new Role;
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);

        $userRole = new Role;
        $userRole->setName('ROLE_USER');
        $manager->persist($userRole);

        $manager->flush();
    }
}
