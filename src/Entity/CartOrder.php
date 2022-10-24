<?php

namespace App\Entity;

use App\Repository\CartOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CartOrderRepository::class)
 */
class CartOrder
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartOrders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="cartOrders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orders;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $quantity;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTotalProductsQuantity()
    {
        $productsArray = [];
        $cartQuantity = $this->quantity;
        $allCartProducts = $this->cart->getCartProducts();
        foreach ($allCartProducts as $currentCartProduct) {
            // dd($currentCartProduct->getProduct());
            $totalQuantity = $currentCartProduct->getTotalQuantity();

            if ($currentCartProduct->getProduct()->getUnity() === 'g') {
                $totalQuantity = $totalQuantity / 1000;
                $currentCartProduct->getProduct()->setUnity('Kg');
            }

            // Creation of $productsArray

            if (!isset($productsArray[$currentCartProduct->getProduct()->getName()])) {
                $productsArray[$currentCartProduct->getProduct()->getName()] = [$currentCartProduct->getProduct()->getUnity() => $totalQuantity * $cartQuantity];
            } else {
                if (isset($productsArray[$currentCartProduct->getProduct()->getName()][$currentCartProduct->getProduct()->getUnity()])) {
                    $productsArray[$currentCartProduct->getProduct()->getName()][$currentCartProduct->getProduct()->getUnity()] += $totalQuantity * $cartQuantity;
                } else {
                    $productsArray[$currentCartProduct->getProduct()->getName()][$currentCartProduct->getProduct()->getUnity()] = $totalQuantity * $cartQuantity;
                }
            }
        }

        return $productsArray;

        // foreach ($allProducts as $currentProduct) {
        //     // dd($currentProduct, $currentProduct->getTotalProducts());
        //     $totalQuantity = $currentProduct->getTotalQuantity();

            // if ($currentProduct->getProduct()->getUnity() === 'g') {
            //     $totalQuantity = $totalQuantity * 1000;
            // }

            // // Creation of $productArray

            // if (!isset($productsArray[$currentProduct->getProduct()->getName()])) {
            //     $productsArray[$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
            // } else {
            //     if (isset($productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
            //         $productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
            //     } else {
            //         $productsArray[$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
            //     }
            // }

        //     // Creation of $depotArray

        //     if (!isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()])) {
        //         $depotArray[$currentProduct->getOrders()->getDepot()->getId()] = [
        //                 $currentProduct->getOrders()->getDepot()->getName() => [
        //                     $currentProduct->getProduct()->getName() => [$currentProduct->getProduct()->getUnity() => $totalQuantity]
        //             ]];
        //     } else {
        //         if (isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()])) {
        //             if(!isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()])) {
        //                 $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()] = [$currentProduct->getProduct()->getUnity() => $totalQuantity];
        //             } else {
        //                 if (isset($depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()])) {
        //                     $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] += $totalQuantity;
        //                 } else {
        //                     $depotArray[$currentProduct->getOrders()->getDepot()->getId()][$currentProduct->getOrders()->getDepot()->getName()][$currentProduct->getProduct()->getName()][$currentProduct->getProduct()->getUnity()] = $totalQuantity;
        //                 }
        //             }
        //         }
        //     }
        // $result = $this->getQuantity() *
    }
}
