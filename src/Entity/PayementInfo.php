<?php

namespace App\Entity;

use App\Repository\PayementInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PayementInfoRepository::class)
 */
class PayementInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payementInfo;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="payementInfo", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayementInfo(): ?string
    {
        return $this->payementInfo;
    }

    public function setPayementInfo(string $payementInfo): self
    {
        $this->payementInfo = $payementInfo;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
