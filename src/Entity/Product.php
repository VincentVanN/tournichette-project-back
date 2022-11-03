<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Vich\Uploadable
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Ce champs ne peut pas être vide", normalizer="trim")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $slug;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull(message="Veuillez sélectionner une unité")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $unity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="products", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Assert\NotNull(message="Veuillez définir un prix")
     * @Assert\PositiveOrZero(message="Le prix ne peut pas être négatif")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $price;

    /**
     * Property asked by front-end
     * 
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $quantity = 1;


    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="Cette description n'est pas valide", allowNull=true, normalizer="trim")
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_products_list"})
     */
    private $colorimetry;

     /**
     * Property asked by front-end
     * 
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $parcel = 1;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Veuillez choisir une catégorie");
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
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

    /**
     * @ORM\Column(type="boolean")
     */
    private $archived;

    /**
     * @ORM\Column(type="integer", options={"default" : 1})
     * @Assert\NotNull(message="Veuillez sélectionner une quantité de base pour ce lot")
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     * @Groups({"api_v1_cart_show"})
     * @Groups({"api_v1_carts_list"})
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $quantityUnity;

    private $totalProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
        // $this->totalProducts = $this->getTotalProducts();
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

    public function setStock(string $stock = '1'): self
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

    public function setImage(?string $image): self
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

    public function setCategory (?Category $category): self
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

    public function getColorimetry(): ?string
    {
        return $this->colorimetry;
    }

    public function setColorimetry(?string $colorimetry): self
    {
        $this->colorimetry = $colorimetry;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of imageFile
     */ 
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     * 
     */ 
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    public function getQuantityUnity(): ?int
    {
        return $this->quantityUnity;
    }

    public function setQuantityUnity(int $quantityUnity = 1): self
    {
        $this->quantityUnity = $quantityUnity;

        return $this;
    }

    public function getTotalProducts()
    {
        $totalProduct = 0;
        $allOrders = $this->orderProducts;
        foreach ($allOrders as $currentOrder)
        {
            $totalProduct += $this->quantityUnity * $currentOrder->getQuantity();
        }

        return $totalProduct;
    }

    /**
     * Set the value of totalProducts
     *
     * @return  self
     */ 
    public function setTotalProducts($totalProducts)
    {
        $totalProduct = 0;
        $allOrders = $this->orderProducts;
        foreach ($allOrders as $currentOrder)
        {
            $totalProduct += $this->quantityUnity * $currentOrder->getQuantity();
        }


        return $this;
    }
}
