<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CartFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{

    public static function getGroups(): array
    {
        return ['groupCart'];
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();

        // Small cart

        $smallCart = new Cart;
        $smallCart->setPrice(10);
        $smallCart->setName("Petit panier");
        $smallCart->setSlug("petit-panier");

        $manager->persist($smallCart);

        $category = 'Fruits';
        $allFruits = $manager->getRepository(Product::class)->findByCategory($category);
        $randomFruits = $faker->randomElements($allFruits, 2);

        foreach($randomFruits as $currentFruit)
        {
            $cartProduct = new CartProduct;
            $cartProduct->setProduct($currentFruit);
            $cartProduct->setCart($smallCart);
            $cartProduct->setQuantity(1);

            $manager->persist($cartProduct);
        }

        $category = 'Légumes';
        $allVegetables = $manager->getRepository(Product::class)->findByCategory($category);
        $randomVegetables = $faker->randomElements($allVegetables, 3);

        foreach($randomVegetables as $currentVegetable)
        {
            $cartProduct = new CartProduct;
            $cartProduct->setProduct($currentVegetable);
            $cartProduct->setCart($smallCart);
            $cartProduct->setQuantity(1);

            $manager->persist($cartProduct);
        }

        // Big cart

        $smallCart = new Cart;
        $smallCart->setPrice(15);
        $smallCart->setName("Grand panier");
        $smallCart->setSlug("grand-panier");

        $manager->persist($smallCart);

        $category = 'Fruits';
        $allFruits = $manager->getRepository(Product::class)->findByCategory($category);
        $randomFruits = $faker->randomElements($allFruits, 3);

        foreach($randomFruits as $currentFruit)
        {
            $cartProduct = new CartProduct;
            $cartProduct->setProduct($currentFruit);
            $cartProduct->setCart($smallCart);
            $cartProduct->setQuantity(1);

            $manager->persist($cartProduct);
        }

        $category = 'Légumes';
        $allVegetables = $manager->getRepository(Product::class)->findByCategory($category);
        $randomVegetables = $faker->randomElements($allVegetables, 4);

        foreach($randomVegetables as $currentVegetable)
        {
            $cartProduct = new CartProduct;
            $cartProduct->setProduct($currentVegetable);
            $cartProduct->setCart($smallCart);
            $cartProduct->setQuantity(1);

            $manager->persist($cartProduct);
        }

        $manager->flush();
    }
}
