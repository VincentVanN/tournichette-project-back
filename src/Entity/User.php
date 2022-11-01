<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="Email déjà existant")
 * @UniqueEntity("apiToken", message="Token déjà existant")
 * @UniqueEntity("sub", message="Sub déjà existant")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotNull
     * @Assert\Email(message = "Cette adresse e-mail n'est pas valide.")
     * @Groups({"api_v1_users_show"})
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_v1_users_show"})
     */
    private $emailChecked;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\Type(
     *      type="bool",
     *      message="La valeur des notifications n'est pas valide. Elle doit être de type boolean ('true' ou 'false').")
     * @Groups({"api_v1_users_show"})
     */
    private $emailNotifications; 

    /**
     * @ORM\Column(type="json")
     * @Assert\NotNull
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Le prénom est obligatoire")
     * @Groups({"api_v1_users_show"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Le nom est obligatoire")
     * @Groups({"api_v1_users_show"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     * @Assert\Positive
     * @Assert\NotNull(message="Le téléphone est obligatoire")
     * @Groups({"api_v1_users_show"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_users_show"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user")
     */
    private $orders;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_v1_users_show"})
     */
    private $stripeCustomerId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apiToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $apiTokenUpdatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $emailTokenUpdatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tempToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $tempTokenUpdatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tempApiToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sub;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        // if (count($this->getRoles()) === 0) {
        //     $roles = ['ROLE_USER'];
        // }
        $this->roles = $roles;

        return $this;
    }

    public function getRoleName(): string
    {
        if (in_array('ROLE_SUPER_ADMIN', $this->getRoles())) {
            return 'Super admin';
        }

        if(in_array('ROLE_ADMIN', $this->getRoles())) {
            return "Admin";
        }

        return "Client";
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;
        $this->setApiTokenUpdatedAt(new DateTimeImmutable());

        return $this;
    }

    public function getApiTokenUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->apiTokenUpdatedAt;
    }

    public function setApiTokenUpdatedAt(?\DateTimeImmutable $apiTokenUpdatedAt): self
    {
        $this->apiTokenUpdatedAt = $apiTokenUpdatedAt;

        return $this;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    public function setSub(?string $sub): self
    {
        $this->sub = $sub;

        return $this;
    }

    /**
     * Get the value of emailChecked
     */ 
    public function isEmailChecked(): ?bool
    {
        return $this->emailChecked;
    }

    /**
     * Set the value of emailChecked
     *
     * @return  self
     */ 
    public function setEmailChecked(bool $emailChecked): self
    {
        $this->emailChecked = $emailChecked;

        return $this;
    }

    /**
     * Get the value of emailToken
     */ 
    public function getEmailToken(): ?string
    {
        return $this->emailToken;
    }

    /**
     * Set the value of emailToken
     *
     * @return  self
     */ 
    public function setEmailToken(?string $emailToken): self
    {
        $this->emailToken = $emailToken;

        return $this;
    }

    /**
     * Get the value of emailTokenUpdatedAt
     */ 
    public function getEmailTokenUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->emailTokenUpdatedAt;
    }

    /**
     * Set the value of emailTokenUpdatedAt
     *
     * @return  self
     */ 
    public function setEmailTokenUpdatedAt(?\DateTimeImmutable $emailTokenUpdatedAt)
    {
        $this->emailTokenUpdatedAt = $emailTokenUpdatedAt;

        return $this;
    }

    /**
     * Get the value of tempToken
     */ 
    public function getTempToken(): ?string
    {
        return $this->tempToken;
    }

    /**
     * Set the value of tempToken
     *
     * @return  self
     */ 
    public function setTempToken(?string $tempToken): self
    {
        $this->tempToken = $tempToken;

        return $this;
    }

    /**
     * Get the value of tempTokenUpdatedAt
     */ 
    public function getTempTokenUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->tempTokenUpdatedAt;
    }

    /**
     * Set the value of tempTokenUpdatedAt
     *
     * @return  self
     */ 
    public function setTempTokenUpdatedAt(?DateTimeImmutable $tempTokenUpdatedAt)
    {
        $this->tempTokenUpdatedAt = $tempTokenUpdatedAt;

        return $this;
    }

    /**
     * Get the value of tempApiToken
     */ 
    public function getTempApiToken(): ?string
    {
        return $this->tempApiToken;
    }

    /**
     * Set the value of tempApiToken
     *
     * @return  self
     */ 
    public function setTempApiToken(?string $token): self
    {
        $this->tempApiToken = $token;

        return $this;
    }

    public function isEmailNotifications(): ?bool
    {
        return $this->emailNotifications;
    }

    public function setEmailNotifications(?bool $emailNotifications): self
    {
        $this->emailNotifications = $emailNotifications;

        return $this;
    }
}
