<?php

namespace App\Entity;

use App\Repository\OrderRepository;
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
     * @ORM\Column(type="integer")
     */
    private $code_order;

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
     * @ORM\Column(type="integer")
     */
    private $code_depot;

    /**
     * @ORM\Column(type="integer")
     */
    private $code_user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeOrder(): ?int
    {
        return $this->code_order;
    }

    public function setCodeOrder(int $code_order): self
    {
        $this->code_order = $code_order;

        return $this;
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

    public function getCodeDepot(): ?int
    {
        return $this->code_depot;
    }

    public function setCodeDepot(int $code_depot): self
    {
        $this->code_depot = $code_depot;

        return $this;
    }

    public function getCodeUser(): ?int
    {
        return $this->code_user;
    }

    public function setCodeUser(int $code_user): self
    {
        $this->code_user = $code_user;

        return $this;
    }
}
