<?php

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
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $secret;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Range(
        notInRangeMessage: 'Liczba graczy musi być w przedziale od 2 do 20',
        min: 2,
        max: 20
    )]
    private int $max_players;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSeasons')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\OneToMany(targetEntity: UserSeasonPlayers::class, mappedBy: 'season', orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToMany(targetEntity: UserSeasonRaces::class, mappedBy: 'season', orphanRemoval: true)]
    private Collection $races;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $completed;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $started;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->races = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): self
    {
        $this->secret = $secret;

        return $this;
    }

    public function getMaxPlayers(): int
    {
        return $this->max_players;
    }

    public function setMaxPlayers(int $max_players): self
    {
        $this->max_players = $max_players;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|UserSeasonPlayers[]
     */
    public function getPlayers(): ?Collection
    {
        return $this->players;
    }

    public function addPlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if (!$this->players->contains($userSeasonPlayer)) {
            $this->players[] = $userSeasonPlayer;
            $userSeasonPlayer->setSeason($this);
        }

        return $this;
    }

    public function removePlayer(UserSeasonPlayers $userSeasonPlayer): self
    {
        if ($this->players->contains($userSeasonPlayer)) {
            $this->players->removeElement($userSeasonPlayer);
            // set the owning side to null (unless already changed)
            if ($userSeasonPlayer->getSeason() === $this) {
                $userSeasonPlayer->setSeason(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserSeasonRaces[]
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(UserSeasonRaces $userSeasonRace): self
    {
        if (!$this->races->contains($userSeasonRace)) {
            $this->races[] = $userSeasonRace;
            $userSeasonRace->setSeason($this);
        }

        return $this;
    }

    public function removeUserSeasonRace(UserSeasonRaces $userSeasonRace): self
    {
        if ($this->races->contains($userSeasonRace)) {
            $this->races->removeElement($userSeasonRace);
            // set the owning side to null (unless already changed)
            if ($userSeasonRace->getSeason() === $this) {
                $userSeasonRace->setSeason(null);
            }
        }

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getStarted(): bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): self
    {
        $this->started = $started;

        return $this;
    }
}
