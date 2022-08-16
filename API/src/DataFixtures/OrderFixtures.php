<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\CartOrder;
use App\Entity\OrderProduct;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            CartFixtures::class,
            ProductFixtures::class,
            UserFixtures::class,
            DepotFixtures::class
        ];
    }

    private $order;
    private $faker;
    private $orderType;

    public function load(ObjectManager $manager): void
    {
        $nbOrders = 50;
        $this->faker = \Faker\Factory::create();
        $faker = $this->faker;
        $randomUsers = $faker->randomElements($manager->getRepository(User::class)->findAll(), 50);
        $allDepots = $manager->getRepository(Depot::class)->findAll();

        for($i = 0 ; $i<$nbOrders; $i++)
        {
            $this->order = new Order();
            $this->orderType = $faker->randomElement(['cart', 'products', 'mix']);

            if ($this->orderType === 'cart') {

               $this->chooseCart($manager);
                
                
            } elseif ($this->orderType === 'products') {
                
                $this->chooseProducts($manager);
                
            } elseif ($this->orderType === 'mix') {

                $this->chooseCart($manager);
                $this->chooseProducts($manager);
            }

            // TODO mix orders (1 cart (big or small) & products)
            

            $order = $this->order;

            $dateOredered = $faker->dateTimeBetween('-1 week');
            // $dateTimeOredered is DateTime object, so whe need to convert it in DateTimeImmutable object :
            $dateOrederedImmutable = $dateOredered instanceof \DateTimeImmutable ? $dateOredered : \DateTimeImmutable::createFromMutable($dateOredered);
            $order->setDateOrder($dateOrederedImmutable);

            $paymentRandomStatus = $faker->randomElement(['yes', 'no']);
            if($paymentRandomStatus === 'yes') {
                $order->setPaidAt($dateOrederedImmutable);
            }
            $order->setPaymentStatus($paymentRandomStatus);

            $order->setDepot($faker->randomElement($allDepots));

            $order->setUser($randomUsers[$i]);

            $deliverRandomStatus = $faker->randomElement(['yes', 'no']);
            $deliveredDate = new \DateTimeImmutable();
            if($deliverRandomStatus === 'yes') {
                $order->setDeliveredAt($deliveredDate);

                if($paymentRandomStatus === 'no') {
                    $order->setPaidAt($deliveredDate);
                    $order->setPaymentStatus('yes');
                }
            }
            $order->setDeliverStatus($deliverRandomStatus);

            $manager->persist($order);

            // $manager->persist($order);
        }


        $manager->flush();
    }

    private function chooseCart($manager)
    {
        $order = $this->order;
        $priceOrder = $order->getPrice() == null ? 0 : $order->getPrice();

        $cartOrder = new CartOrder();
        $cartOrder->setQuantity(1);
        $cartOrder->setOrders($order);
        

        $cart = $manager->getRepository(Cart::class)->findOneBy(['type_cart' => $this->faker->randomElement(['small', 'big'])]);
        // $order->setPrice($cart->getPrice());
        $cartOrder->setCart($cart);

        $order->setPrice($priceOrder + $cart->getPrice());

        $manager->persist($cartOrder);

        $this->order = $order;

        return $this;
    }

    private function chooseProducts($manager)
    {
        $faker = $this->faker;
        $order = $this->order;
        $priceOrder = $order->getPrice() == null ? 0 : $order->getPrice();

        $nbProducts = $faker->numberBetween(1, 5);
        $randomProducts = $faker->randomElements(
                                $manager->getRepository(Product::class)->findAll(),
                                $faker->numberBetween(1, 5)
                            );

        foreach($randomProducts as $currentProduct) {
            $orderProduct = new OrderProduct;
            $orderProduct->setProduct($currentProduct);
            $orderProduct->setOrders($order);
            $orderProduct->setQuantity(1);
            $priceOrder += $currentProduct->getPrice();

            $manager->persist($orderProduct);
        }

        $order->setPrice($priceOrder);

        $this->order = $order;

        return $this;
    }
}
