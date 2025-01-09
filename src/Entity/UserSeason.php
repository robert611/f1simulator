<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserSeasonRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Istnieje już liga z taką nazwą')]
class UserSeason
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'secret', type: 'string', length: 255, unique: true, nullable: false)]
    private string $secret;

    #[ORM\Column(name: 'max_players', type: 'smallint', nullable: false)]
    #[Assert\Range(
        notInRangeMessage: 'Liczba graczy musi być w przedziale od 2 do 20',
        min: 2,
        max: 20
    )]
    private int $maxPlayers;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSeasons')]
    #[ORM\JoinColumn(name: 'owner_id', nullable: false)]
    private User $owner;

    #[ORM\Column(name: 'name', type: 'string', length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'completed', type: 'boolean', nullable: false)]
    private bool $completed;

    #[ORM\Column(name: 'started', type: 'boolean', nullable: false)]
    private bool $started;

    #[ORM\OneToMany(targetEntity: UserSeasonPlayers::class, mappedBy: 'season', orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToMany(targetEntity: UserSeasonRaces::class, mappedBy: 'season', orphanRemoval: true)]
    private Collection $races;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->races = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): void
    {
        $this->maxPlayers = $maxPlayers;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return Collection<UserSeasonPlayers>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(UserSeasonPlayers $userSeasonPlayer): void
    {
        if (!$this->players->contains($userSeasonPlayer)) {
            $this->players[] = $userSeasonPlayer;
            $userSeasonPlayer->setSeason($this);
        }
    }

    public function removePlayer(UserSeasonPlayers $userSeasonPlayer): void
    {
        if ($this->players->contains($userSeasonPlayer)) {
            $this->players->removeElement($userSeasonPlayer);
        }
    }

    /**
     * @return Collection<UserSeasonRaces>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(UserSeasonRaces $userSeasonRace): void
    {
        if (!$this->races->contains($userSeasonRace)) {
            $this->races[] = $userSeasonRace;
            $userSeasonRace->setSeason($this);
        }
    }

    public function removeUserSeasonRace(UserSeasonRaces $userSeasonRace): void
    {
        if ($this->races->contains($userSeasonRace)) {
            $this->races->removeElement($userSeasonRace);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    public function getStarted(): bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): void
    {
        $this->started = $started;
    }
}
