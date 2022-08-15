<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_order;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $payment_status;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $deliver_status;

    /**
     * @ORM\ManyToOne(targetEntity=Depot::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $depot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="orders")
     */
    private $orderProducts;

    /**
     * @ORM\OneToMany(targetEntity=CartOrder::class, mappedBy="orders")
     */
    private $cartOrders;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->cartOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->date_order;
    }

    public function setDateOrder(\DateTimeInterface $date_order): self
    {
        $this->date_order = $date_order;

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

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function getDeliverStatus(): ?string
    {
        return $this->deliver_status;
    }

    public function setDeliverStatus(string $deliver_status): self
    {
        $this->deliver_status = $deliver_status;

        return $this;
    }

    public function getDepot(): ?Depot
    {
        return $this->depot;
    }

    public function setDepot(?Depot $depot): self
    {
        $this->depot = $depot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $orderProduct->setOrders($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getOrders() === $this) {
                $orderProduct->setOrders(null);
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
            $cartOrder->setOrders($this);
        }

        return $this;
    }

    public function removeCartOrder(CartOrder $cartOrder): self
    {
        if ($this->cartOrders->removeElement($cartOrder)) {
            // set the owning side to null (unless already changed)
            if ($cartOrder->getOrders() === $this) {
                $cartOrder->setOrders(null);
            }
        }

        return $this;
    }
}
