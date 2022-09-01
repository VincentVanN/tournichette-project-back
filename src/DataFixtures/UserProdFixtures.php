<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProdFixtures extends Fixture implements FixtureGroupInterface
{
    private $userPasswordHasher;

    public static function getGroups(): array
    {
        return ['prodFixtures'];
    }

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $superAdminUser = new User;

        $superAdminUser->setEmail('superadmin@tournichette.fr');
        $superAdminUser->setFirstname('Super');
        $superAdminUser->setLastname('Admin');

        $superAdminUser->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdminUser->setPassword($this->userPasswordHasher->hashPassword($superAdminUser, 'yFa@HA9RM8B9AkbQkPF4yFa@HA9RM8B9AkbQkPF4'));
        $superAdminUser->setPhone('0606060607');

        $superAdminUser->setAddress('1, rue de la Tournichette, 59144 Wargnies-le-Petit');

        $manager->persist($superAdminUser);

        // ###################

        $adminUser = new User;
        
        $adminUser->setEmail('admin@tournichette.com');
        $adminUser->setFirstname('Junior');
        $adminUser->setLastname('Admin');

        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setPassword($this->userPasswordHasher->hashPassword($adminUser, 'yFa@HA9RM8B9AkbQkPF4'));
        $adminUser->setPhone('0606060608');

        $adminUser->setAddress('1bis, rue de la Tournichette, 59144 Wargnies-le-Petit');

        $manager->persist($adminUser);

        // ###################
        
        $manager->flush();
    }
}
