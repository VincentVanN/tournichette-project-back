<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $slug;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=3)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $unity;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $image;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $price;

    /**
     * Property asked by front-end
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $quantity = 1;

     /**
     * Property asked by front-end
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     */
    private $parcel = 1;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_v1_products_list"})
     */
    private $category;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, inversedBy="productReplace", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="product")
     */
    private $cartProducts;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="product")
     */
    private $orderProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
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

    public function getStock(): ?string
    {
        return $this->stock;
    }

    public function setStock(string $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProduct(): ?self
    {
        return $this->product;
    }

    public function setProduct(?self $product): self
    {
        $this->product = $product;

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
            $cartProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getProduct() === $this) {
                $cartProduct->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setProduct($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getProduct() === $this) {
                $orderProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }
 
    public function getParcel()
    {
        return $this->parcel;
    }
}
