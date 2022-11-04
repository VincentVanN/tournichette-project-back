<?php

namespace App\Entity;

use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 * @UniqueEntity("name", message="Ce panier existe déjà")
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champs ne doit pas être vide")
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotNull(message="Vous devez définir un prix")
     * @Assert\PositiveOrZero(message="Le prix ne doit pas être négatif")
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $price;


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     */
    private $slug;

    /**
     * Property asked by front-end
     * 
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     */
    private $quantity = 1;

    /**
     * Property asked by front-end
     * 
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     */
    private $parcel = 1;

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

    /**
     * @ORM\Column(type="boolean")
     */
    private $onSale;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get property asked by front-end
     */ 
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Get property asked by front-end
     */ 
    public function getParcel()
    {
        return $this->parcel;
    }

    public function isOnSale(): ?bool
    {
        return $this->onSale;
    }

    public function setOnSale(bool $onSale): self
    {
        $this->onSale = $onSale;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;
        if ($archived) {
            $this->setArchivedAt(new \DateTimeImmutable());
            $this->setOnSale(false);
        }

        return $this;
    }

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeImmutable $archivedAt): self
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }
}
