<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_order_user_show"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_order_user_show"})
     */
    private $type_cart;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="cart")
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_carts_list"})
     */
    private $cartProducts;

    /**
     * @ORM\OneToMany(targetEntity=CartOrder::class, mappedBy="cart")
     */
    private $cartOrders;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
        $this->cartOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTypeCart(): ?string
    {
        return $this->type_cart;
    }

    public function setTypeCart(string $type_cart): self
    {
        $this->type_cart = $type_cart;

        return $this;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
            $cartProduct->setCart($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getCart() === $this) {
                $cartProduct->setCart(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CartOrder>
     */
    public function getCartOrders(): Collection
    {
        return $this->cartOrders;
    }

    public function addCartOrder(CartOrder $cartOrder): self
    {
        if (!$this->cartOrders->contains($cartOrder)) {
            $this->cartOrders[] = $cartOrder;
            $cartOrder->setCart($this);
        }

        return $this;
    }

    public function removeCartOrder(CartOrder $cartOrder): self
    {
        if ($this->cartOrders->removeElement($cartOrder)) {
            // set the owning side to null (unless already changed)
            if ($cartOrder->getCart() === $this) {
                $cartOrder->setCart(null);
            }
        }

        return $this;
    }
}
