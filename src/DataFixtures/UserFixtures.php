<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $userPasswordHasher;

    public static function getGroups(): array
    {
        return ['devFixtures'];
    }

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create();
        $fakerFr = \Faker\Factory::create('fr_FR');

        // ###################

        $superAdminUser = new User;

        $superAdminUser->setEmail('superadmin@tournichette.com');
        $superAdminUser->setFirstname($faker->firstName());
        $superAdminUser->setLastname($faker->lastName());

        $superAdminUser->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdminUser->setPassword($this->userPasswordHasher->hashPassword($superAdminUser, 'admin'));


        $phoneSuperAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceSuperAdmin = str_replace(' ', '', $phoneSuperAdmin);
        $superAdminUser->setPhone($phoneNoSpaceSuperAdmin);

        $superAdminUser->setAddress($faker->address());

        $manager->persist($superAdminUser);

        // ###################

        $adminUser = new User;
        
        $adminUser->setEmail('admin@tournichette.com');
        $adminUser->setFirstname($faker->firstName());
        $adminUser->setLastname($faker->lastName());

        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, 'admin'));

        $phoneAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceAdmin = str_replace(' ', '', $phoneAdmin);
        $adminUser->setPhone($phoneNoSpaceAdmin);

        $adminUser->setAddress($faker->address());

        $manager->persist($adminUser);

        // ###################

        $nbUser = 100;

        for($i = 0; $i < $nbUser; $i++)
        {
            $userObj = new User();
            
            // $userObj->setRole($currentUserRole);

            if ($i === 0) {
                // Create a specific user test
                $userObj->setEmail('user@user.com');
            } else {
                $userObj->setEmail($faker->unique()->email());
            }

            $userObj->setFirstname($faker->firstName());
            $userObj->setLastname($faker->lastName());

            $userObj->setRoles(['ROLE_USER']);
            $userObj->setPassword($this->userPasswordHasher->hashPassword($userObj, 'user'));

            $phoneUser = $fakerFr->unique()->serviceNumber();
            $phoneNoSpaceUser = str_replace(' ', '', $phoneUser);
            $userObj->setPhone($phoneNoSpaceUser);

            $userObj->setAddress($faker->address());

            $manager->persist($userObj);
        }
        
        $manager->flush();
    }
}
