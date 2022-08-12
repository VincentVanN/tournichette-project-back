<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="integer")
     */
    private $code_cart;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type_cart;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $test;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCart(): ?int
    {
        return $this->code_cart;
    }

    public function setCodeCart(int $code_cart): self
    {
        $this->code_cart = $code_cart;

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

    public function getTypeCart(): ?string
    {
        return $this->type_cart;
    }

    public function setTypeCart(string $type_cart): self
    {
        $this->type_cart = $type_cart;

        return $this;
    }

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(string $test): self
    {
        $this->test = $test;

        return $this;
    }
}
