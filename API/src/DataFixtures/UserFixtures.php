<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordHash;

    public function __construct(UserPasswordHasherInterface $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function getDependencies()
    {
        return [
            RoleFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        
        $faker = \Faker\Factory::create();
        $fakerFr = \Faker\Factory::create('fr_FR');

        $superAdminUser = new User;
        $superAdminRole = $manager->getRepository(Role::class)->findBy(['name' => 'ROLE_SUPER_ADMIN']);
        foreach ($superAdminRole as $adminRole) {
            $superAdminUser->setRole($adminRole);
        }

        $superAdminUser->setEmail('superadmin@tournichette.com');
        $superAdminUser->setFirstname($faker->firstName());
        $superAdminUser->setLastname($faker->lastName());

        // $hashedPassword = $this->passwordHash->hashPassword($superAdminUser, 'admin');
        $superAdminUser->setPassword('admin');

        $phoneSuperAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceSuperAdmin = str_replace(' ', '', $phoneSuperAdmin);
        $superAdminUser->setPhone($phoneNoSpaceSuperAdmin);

        $superAdminUser->setAddress($faker->address());

        $superAdminUser->setToken($faker->unique()->sha256());
        // dd($superAdminRole, $superAdminUser);
        $manager->persist($superAdminUser);

        // $manager->flush();

        $adminUser = new User;
        // $superAdminRole = new Role;
        $adminRole = $manager->getRepository(Role::class)->findBy(['name' => 'ROLE_ADMIN']);
        foreach ($adminRole as $currentAdminRole) {
            $adminUser->setRole($currentAdminRole);
        }

        $adminUser->setEmail('superadmin@tournichette.com');
        $adminUser->setFirstname($faker->firstName());
        $adminUser->setLastname($faker->lastName());

        // $hashedPassword = $this->passwordHash->hashPassword($adminUser, 'admin');
        $adminUser->setPassword('admin');

        $phoneAdmin = $fakerFr->unique()->serviceNumber();
        $phoneNoSpaceAdmin = str_replace(' ', '', $phoneAdmin);
        // dd($phoneAdmin, $phoneNoSpaceAdmin);
        $adminUser->setPhone($phoneNoSpaceAdmin);

        $adminUser->setAddress($faker->address());

        $adminUser->setToken($faker->unique()->sha256());
        // dd($superAdminRole, $adminUser);
        $manager->persist($adminUser);

        $nbUser = 50;

        for($i = 0; $i < $nbUser; $i++)
        {
            $userObj = new User();
            $userRole = $manager->getRepository(Role::class)->findBy(['name' => 'ROLE_USER']);
            foreach ($userRole as $currentUserRole) {
                $userObj->setRole($currentUserRole);
            }

            $userObj->setEmail($faker->unique()->email());
            $userObj->setFirstname($faker->firstName());
            $userObj->setLastname($faker->lastName());

            $userObj->setPassword('user');

            $phoneUser = $fakerFr->unique()->serviceNumber();
            $phoneNoSpaceUser = str_replace(' ', '', $phoneUser);
            // dd($phoneAdmin, $phoneNoSpaceAdmin);
            $userObj->setPhone($phoneNoSpaceUser);

            $userObj->setAddress($faker->address());

            $userObj->setToken($faker->unique()->sha256());
            // dd($superAdminRole, $adminUser);
            $manager->persist($userObj);
        }


        $manager->flush();
    }
}
