<?php

namespace App\Entity;

use App\Repository\SalesStatusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SalesStatusRepository::class)
 */
class SalesStatus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enable;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $startMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $startMailSubject;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $endMail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sendMail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $endMailSubject;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

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

    public function getEndMail(): ?string
    {
        return $this->endMail;
    }

    public function setEndMail(?string $endMail): self
    {
        $this->endMail = $endMail;

        return $this;
    }

    public function getStartMail(): ?string
    {
        return $this->startMail;
    }

    public function setStartMail(?string $startMail): self
    {
        $this->startMail = $startMail;

        return $this;
    }

    public function isSendMail(): ?bool
    {
        return $this->sendMail;
    }

    public function setSendMail(bool $sendMail): self
    {
        $this->sendMail = $sendMail;

        return $this;
    }

    /**
     * Get the value of sendMailSubject
     */ 
    public function getEndMailSubject(): ?string
    {
        return $this->endMailSubject;
    }

    /**
     * Set the value of sendMailSubject
     *
     * @return  self
     */ 
    public function setEndMailSubject(?string $endMailSubject): self
    {
        $this->endMailSubject = $endMailSubject;

        return $this;
    }

    /**
     * Get the value of startMailSubject
     */ 
    public function getStartMailSubject(): ?string
    {
        return $this->startMailSubject;
    }

    /**
     * Set the value of startMailSubject
     *
     * @return  self
     */ 
    public function setStartMailSubject(?string $startMailSubject): self
    {
        $this->startMailSubject = $startMailSubject;

        return $this;
    }
}
