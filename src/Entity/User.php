<?php

declare(strict_types=1);

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
#[UniqueEntity(fields: ['email'], message: 'Istnieje już konto z takim adresem email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'username', type: 'string', length: 180, unique: true, nullable: false)]
    private string $username;

    #[ORM\Column(name: 'email', type: 'string', length: 180, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column(name: 'roles', type: 'json', nullable: false)]
    private array $roles = [];

    #[ORM\Column(name: 'password', type: 'string', nullable: false)]
    private string $password;

    #[ORM\OneToMany(targetEntity: Season::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $seasons;

    #[ORM\OneToMany(targetEntity: UserSeason::class, mappedBy: 'owner')]
    private Collection $userSeasons;

    #[ORM\OneToMany(targetEntity: UserSeasonPlayer::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userSeasonPlayers;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->userSeasons = new ArrayCollection();
        $this->userSeasonPlayers = new ArrayCollection();
    }

    public function getId(): int
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
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
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
     * @return Collection<Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): void
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setUser($this);
        }
    }

    public function removeSeason(Season $season): void
    {
        if ($this->seasons->contains($season)) {
            $this->seasons->removeElement($season);
        }
    }

    /**
     * @return Collection<UserSeason>
     */
    public function getUsersSeasons(): Collection
    {
        return $this->userSeasons;
    }

    public function addUsersSeason(UserSeason $userSeason): void
    {
        if (!$this->userSeasons->contains($userSeason)) {
            $this->userSeasons[] = $userSeason;
            $userSeason->setOwner($this);
        }
    }

    public function removeUsersSeason(UserSeason $userSeason): void
    {
        if ($this->userSeasons->contains($userSeason)) {
            $this->userSeasons->removeElement($userSeason);
        }
    }

    /**
     * @return Collection<UserSeasonPlayer>
     */
    public function getUserSeasonPlayers(): Collection
    {
        return $this->userSeasonPlayers;
    }

    public function addUserSeasonPlayer(UserSeasonPlayer $userSeasonPlayer): void
    {
        if (!$this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers[] = $userSeasonPlayer;
            $userSeasonPlayer->setUser($this);
        }
    }

    public function removeUserSeasonPlayer(UserSeasonPlayer $userSeasonPlayer): void
    {
        if ($this->userSeasonPlayers->contains($userSeasonPlayer)) {
            $this->userSeasonPlayers->removeElement($userSeasonPlayer);
        }
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}
