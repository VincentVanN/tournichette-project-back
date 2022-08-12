<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $code_product;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=3)
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $unity;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $image;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $code_product_replace;

    /**
     * @ORM\Column(type="integer")
     */
    private $code_category;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeProduct(): ?int
    {
        return $this->code_product;
    }

    public function setCodeProduct(int $code_product): self
    {
        $this->code_product = $code_product;

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

    public function getCodeProductReplace(): ?int
    {
        return $this->code_product_replace;
    }

    public function setCodeProductReplace(?int $code_product_replace): self
    {
        $this->code_product_replace = $code_product_replace;

        return $this;
    }

    public function getCodeCategory(): ?int
    {
        return $this->code_category;
    }

    public function setCodeCategory(int $code_category): self
    {
        $this->code_category = $code_category;

        return $this;
    }
}
