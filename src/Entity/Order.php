<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @Groups({"api_v1_orders_user"})
     * @Groups({"api_v1_order_user_show"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $orderedAt;

    /**
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=5)
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $payment_status;

    /**
     * @ORM\Column(type="string", length=5)
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $deliver_status;

    /**
     * @ORM\ManyToOne(targetEntity=Depot::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $depot;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="orders")
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $orderProducts;

    /**
     * @ORM\OneToMany(targetEntity=CartOrder::class, mappedBy="orders")
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $cartOrders;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $paidAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deliveredAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentId;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
        $this->cartOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Groups({"api_v1_orders_user"})
     * @Groups({"api_v1_order_user_show"})
     */
    public function getDateOrder()
    {
        return date_format($this->orderedAt, 'd-m-Y H:i:s');
    }

    /**
     */
    public function setDateOrder(\DateTimeImmutable $orderedAt): self
    {
        $this->orderedAt = $orderedAt;

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

    /**
     * @var $payement_status = "yes | no"
     */
    public function setPaymentStatus(string $payment_status): self
    {
        $this->payment_status = $payment_status;
        if ($this->getPaymentStatus() == 'yes') {
            $this->setPaidAt(new \DateTimeImmutable());
        }

        return $this;
    }

    public function getDeliverStatus(): ?string
    {
        return $this->deliver_status;
    }

    public function setDeliverStatus(string $deliver_status): self
    {
        $this->deliver_status = $deliver_status;
        if ($this->getDeliverStatus() == 'yes') {
            $this->setDeliveredAt(new \DateTimeImmutable());
        }

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

    public function resetOrderProducts(): self
    {
        foreach ($this->orderProducts as $currentOrderProduct) {
            $this->removeOrderProduct($currentOrderProduct);
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

    public function resetCartOrders(): self
    {
        foreach ($this->cartOrders as $currentCartOrder) {
            $this->removeCartOrder($currentCartOrder);
        }

        return $this;
    }

    /**
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    public function getPaidAt()
    {
        $humanDate = $this->paidAt !== null ? date_format($this->paidAt, 'd-m-Y H:i:s') : $this->paidAt;
        return $humanDate;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    /**
     * @Groups({"api_v1_order_user_show"})
     * @Groups({"api_v1_orders_user"})
     */
    public function getDeliveredAt()
    {
        $humanDate = $this->deliveredAt !== null ? date_format($this->deliveredAt, 'd-m-Y H:i:s') : $this->deliveredAt;
        return $humanDate;
    }

    public function setDeliveredAt(?\DateTimeImmutable $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

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

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(?string $paymentId): self
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getTotalPriceOrder(int $depotId) 
    {   $total = 0;
        foreach ($this->getPrice() as $price){
        $total += $price->totalPriceOrder();
        }
         return $total;
    }
}
