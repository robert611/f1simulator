<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Istnieje już konto z takim loginem")
 * @UniqueEntity(fields={"email"}, message="Istnieje już konto z takim emailem")
 */
class User implements UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Season", mappedBy="user", orphanRemoval=true)
     */
    private $seasons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSeason", mappedBy="owner")
     */
    private $userSeasons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSeasonPlayers", mappedBy="user", orphanRemoval=true)
     */
    private $userSeasonPlayers;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->usersSeasons = new ArrayCollection();
        $this->userSeasonPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
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
     * @return Collection|Season[]
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setUser($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->contains($season)) {
            $this->seasons->removeElement($season);
            // set the owning side to null (unless already changed)
            if ($season->getUser() === $this) {
                $season->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UsersSeason[]
     */
    public function getUsersSeasons(): Collection
    {
        return $this->usersSeasons;
    }

    public function addUsersSeason(UserSeason $userSeason): self
    {
        if (!$this->userSeason->contains($userSeason)) {
            $this->userSeason[] = $userSeason;
            $userSeason->setOwner($this);
        }

        return $this;
    }

    public function removeUsersSeason(UserSeason $userSeason): self
    {
        if ($this->userSeason->contains($userSeason)) {
            $this->userSeason->removeElement($userSeason);
            // set the owning side to null (unless already changed)
            if ($userSeason->getOwner() === $this) {
                $userSeason->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserSeasonPlayers[]
     */
    public function getUserSeasonPlayers(): Collection
    {
        return $this->userSeasonPlayers;
    }

    public function addUserSeasonPlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if (!$this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers[] = $userSeasonPlayer;
            $userSeasonPlayer->setUser($this);
        }

        return $this;
    }

    public function removeUserSeasonPlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if ($this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers->removeElement($userSeasonPlayer);
            // set the owning side to null (unless already changed)
            if ($userSeasonPlayer->getUser() === $this) {
                $userSeasonPlayer->setUser(null);
            }
        }

        return $this;
    }
}
