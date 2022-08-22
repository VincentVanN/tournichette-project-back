<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_categories_list"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Groups({"api_v1_categories_list"})
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_categories_list"})
     * @Groups({"api_v1_category_product"})
     * @Groups({"api_v1_products_list"})
     * @Groups({"api_v1_product_show"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"api_v1_categories_list"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="Category")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }


    public function getId(): ?int
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
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
}