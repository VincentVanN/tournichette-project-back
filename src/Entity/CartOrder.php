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

    // public function getTotalProductQuantity()
    // {
    //     $result = $this->getQuantity() *
    // }
}
