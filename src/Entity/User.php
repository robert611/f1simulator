<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'Istnieje już konto z takim loginem')]
#[UniqueEntity(fields: ['email'], message: 'Istnieje już konto z takim emailem')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: false)]
    private string $username;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(type: 'json', nullable: false)]
    private array $roles = [];

    #[ORM\Column(type: 'string', nullable: false)]
    private string $password;

    #[ORM\OneToMany(targetEntity: Season::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $seasons;

    #[ORM\OneToMany(targetEntity: UserSeason::class, mappedBy: 'owner')]
    private Collection $userSeasons;

    #[ORM\OneToMany(targetEntity: UserSeasonPlayers::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userSeasonPlayers;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->userSeasons = new ArrayCollection();
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
    public function eraseCredentials(): void
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
     * @return Collection|UserSeason[]
     */
    public function getUsersSeasons(): Collection
    {
        return $this->userSeasons;
    }

    public function addUsersSeason(UserSeason $userSeason): self
    {
        if (!$this->userSeasons->contains($userSeason)) {
            $this->userSeasons[] = $userSeason;
            $userSeason->setOwner($this);
        }

        return $this;
    }

    public function removeUsersSeason(UserSeason $userSeason): self
    {
        if ($this->userSeasons->contains($userSeason)) {
            $this->userSeasons->removeElement($userSeason);
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

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
