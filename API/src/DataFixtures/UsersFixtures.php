<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UsersFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create();
        $fakerFr = \Faker\Factory::create('fr_FR');

        // ###################

        $superAdminUser = new Users;

        $superAdminUser->setEmail('superadmin@tournichette.com');
        $superAdminUser->setFirstname($faker->firstName());
        $superAdminUser->setLastname($faker->lastName());

        // $superAdminUser->setRole($adminRole);
        // $superAdminUser->setPassword('admin');


        $phoneSuperAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceSuperAdmin = str_replace(' ', '', $phoneSuperAdmin);
        $superAdminUser->setPhone($phoneNoSpaceSuperAdmin);

        $superAdminUser->setAddress($faker->address());
        $manager->persist($superAdminUser);

        // ###################

        $adminUser = new Users;
        
        $adminUser->setEmail('admin@tournichette.com');
        $adminUser->setFirstname($faker->firstName());
        $adminUser->setLastname($faker->lastName());

        // $adminUser->setRole($currentAdminRole);
        // $adminUser->setPassword('admin');

        $phoneAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceAdmin = str_replace(' ', '', $phoneAdmin);
        $adminUser->setPhone($phoneNoSpaceAdmin);

        $adminUser->setAddress($faker->address());

        $manager->persist($adminUser);

        // ###################

        $nbUser = 100;

        for($i = 0; $i < $nbUser; $i++)
        {
            $userObj = new Users();
            
            // $userObj->setRole($currentUserRole);

            if ($i === 0) {
                // Create a specific user test
                $userObj->setEmail('user@user.com');
            } else {
                $userObj->setEmail($faker->unique()->email());
            }

            $userObj->setFirstname($faker->firstName());
            $userObj->setLastname($faker->lastName());

            // $userObj->setPassword('user');

            $phoneUser = $fakerFr->unique()->serviceNumber();
            $phoneNoSpaceUser = str_replace(' ', '', $phoneUser);
            $userObj->setPhone($phoneNoSpaceUser);

            $userObj->setAddress($faker->address());

            $manager->persist($userObj);
        }
        
        $manager->flush();
    }
}
