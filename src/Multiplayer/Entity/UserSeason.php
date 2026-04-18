<?php

declare(strict_types=1);

namespace Multiplayer\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Multiplayer\Repository\UserSeasonRepository;
use Security\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserSeasonRepository::class)]
#[ORM\Table(name: 'user_season')]
#[UniqueEntity(fields: ['name'], message: 'Istnieje już liga z taką nazwą')]
class UserSeason
{
    public const int MINIMUM_PLAYERS = 2;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
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

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', nullable: false)]
    private User $owner;

    #[ORM\Column(name: 'name', type: 'string', length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name: 'started', type: 'boolean', nullable: false)]
    private bool $started;

    #[ORM\Column(name: 'completed', type: 'boolean', nullable: false)]
    private bool $completed;

    #[ORM\Column(name: 'started_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $startedAt;

    #[ORM\Column(name: 'completed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $completedAt;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: UserSeasonPlayer::class, mappedBy: 'season', orphanRemoval: true)]
    private Collection $players;

    #[ORM\OneToMany(targetEntity: UserSeasonRace::class, mappedBy: 'season', orphanRemoval: true)]
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

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function getStarted(): bool
    {
        return $this->started;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<UserSeasonPlayer>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(UserSeasonPlayer $userSeasonPlayer): void
    {
        if (!$this->players->contains($userSeasonPlayer)) {
            $this->players[] = $userSeasonPlayer;
            $userSeasonPlayer->setSeason($this);
        }
    }

    public function removePlayer(UserSeasonPlayer $userSeasonPlayer): void
    {
        if ($this->players->contains($userSeasonPlayer)) {
            $this->players->removeElement($userSeasonPlayer);
        }
    }

    /**
     * @return Collection<UserSeasonRace>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function addRace(UserSeasonRace $userSeasonRace): void
    {
        if (!$this->races->contains($userSeasonRace)) {
            $this->races[] = $userSeasonRace;
            $userSeasonRace->setSeason($this);
        }
    }

    public function removeUserSeasonRace(UserSeasonRace $userSeasonRace): void
    {
        if ($this->races->contains($userSeasonRace)) {
            $this->races->removeElement($userSeasonRace);
        }
    }

    public static function create(
        string $secret,
        int $maxPlayers,
        User $owner,
        string $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        bool $started = false,
        bool $completed = false,
    ): self {
        $userSeason = new self();
        $userSeason->secret = $secret;
        $userSeason->maxPlayers = $maxPlayers;
        $userSeason->owner = $owner;
        $userSeason->name = $name;
        $userSeason->started = $started;
        $userSeason->completed = $completed;
        $userSeason->startedAt = null;
        $userSeason->completedAt = null;
        $userSeason->createdAt = $createdAt;
        $userSeason->updatedAt = $updatedAt;

        return $userSeason;
    }

    public function start(DateTimeImmutable $startedAt): void
    {
        $this->started = true;
        $this->startedAt = $startedAt;
    }

    public function end(DateTimeImmutable $completedAt): void
    {
        $this->completed = true;
        $this->completedAt = $completedAt;
    }

    /**
     * @return int[]
     */
    public function getLeagueDriversIds(): array
    {
        $leagueDriversIds = [];

        foreach ($this->getPlayers() as $player) {
            $leagueDriversIds[] = $player->getDriverId();
        }

        return $leagueDriversIds;
    }
}
