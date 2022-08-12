<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
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
    private $code_role;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeRole(): ?int
    {
        return $this->code_role;
    }

    public function setCodeRole(int $code_role): self
    {
        $this->code_role = $code_role;

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
}
