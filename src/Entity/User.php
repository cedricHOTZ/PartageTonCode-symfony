<?php

namespace App\Entity;

use Symfony\Component;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"},message="Un autre utilisateur s'est déjà inscrit avec cette adresse email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /** 
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;


    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;




    /**
     * @ORM\OneToMany(targetEntity=Pin::class, mappedBy="user", orphanRemoval=true)
     */
    private $pins;

    /**
     * @ORM\Column(type="string", name="facebook_id", nullable=true)
     */
    protected $facebookId;

    /**
     * @ORM\Column(type="string", name="google_id", nullable=true)
     */
    protected $googleId;


    public function __construct()
    {
        $this->pins = new ArrayCollection();
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
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of isVerified
     */
    public function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set the value of isVerified
     *
     * @return  self
     */
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }


    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }



    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|Pin[]
     */
    public function getPins(): \Doctrine\Common\Collections\Collection
    {
        return $this->pins;
    }

    public function addPin(Pin $pin): self
    {
        if (!$this->pins->contains($pin)) {
            $this->pins[] = $pin;
            $pin->setUser($this);
        }

        return $this;
    }

    public function removePin(Pin $pin): self
    {
        if ($this->pins->contains($pin)) {
            $this->pins->removeElement($pin);
            // set the owning side to null (unless already changed)
            if ($pin->getUser() === $this) {
                $pin->setUser(null);
            }
        }

        return $this;
    }


    

    /**
     * Get the value of facebookId
     */ 
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set the value of facebookId
     *
     * @return  self
     */ 
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get the value of googleId
     */ 
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set the value of googleId
     *
     * @return  self
     */ 
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }
}
