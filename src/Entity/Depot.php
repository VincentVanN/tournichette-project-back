<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 * @UniqueEntity("name", message="Ce dépot existe déjà")
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_depots_list"})
     * @Groups({"api_v1_depot_show"})
     * 
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"api_v1_depots_list"})
     * @Groups({"api_v1_depot_show"})
     * 
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"api_v1_depots_list"})
     * @Groups({"api_v1_depot_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"api_v1_depots_list"})
     * @Groups({"api_v1_depot_show"})
     * @Groups({"api_v1_orders_user"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="depot")
     */
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setDepot($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getDepot() === $this) {
                $order->setDepot(null);
            }
        }

        return $this;
    }
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
