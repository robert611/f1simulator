<?php

declare(strict_types=1);

namespace Multiplayer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Domain\Contract\Model\DriverPodiumsDictionary;
use Domain\Contract\Model\DriverPodiumsDTO;
use Multiplayer\Repository\UserSeasonPlayersRepository;
use Security\Entity\User;

#[ORM\Entity(repositoryClass: UserSeasonPlayersRepository::class)]
#[ORM\Table('user_season_player')]
class UserSeasonPlayer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: UserSeason::class, inversedBy: 'players')]
    #[ORM\JoinColumn(name: 'season_id', nullable: false)]
    private UserSeason $season;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\Column(name: 'driver_id', type: 'integer', nullable: false)]
    private int $driverId;

    #[ORM\Column(name: 'points', type: 'integer', nullable: false)]
    private int $points = 0;

    #[ORM\Column(name: 'position', type: 'integer', nullable: false)]
    private int $position = 0;

    #[ORM\OneToMany(targetEntity: UserSeasonRaceResult::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $raceResults;

    #[ORM\OneToMany(targetEntity: UserSeasonQualification::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $qualificationsResults;

    public function __construct()
    {
        $this->raceResults = new ArrayCollection();
        $this->qualificationsResults = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSeason(): UserSeason
    {
        return $this->season;
    }

    public function setSeason(UserSeason $season): void
    {
        $this->season = $season;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function setDriverId(int $driverId): void
    {
        $this->driverId = $driverId;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return Collection<UserSeasonRaceResult>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(UserSeasonRaceResult $raceResult): void
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setPlayer($this);
        }
    }

    public function removeRaceResult(UserSeasonRaceResult $raceResult): void
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }
    }

    /**
     * @return Collection<UserSeasonQualification>
     */
    public function getQualificationsResults(): Collection
    {
        return $this->qualificationsResults;
    }

    public function addQualificationsResult(UserSeasonQualification $qualificationsResult): void
    {
        if (!$this->qualificationsResults->contains($qualificationsResult)) {
            $this->qualificationsResults[] = $qualificationsResult;
            $qualificationsResult->setPlayer($this);
        }
    }

    public function removeQualificationsResult(UserSeasonQualification $qualificationsResult): void
    {
        if ($this->qualificationsResults->contains($qualificationsResult)) {
            $this->qualificationsResults->removeElement($qualificationsResult);
        }
    }

    public static function create(UserSeason $userSeason, User $user, int $driverId): self
    {
        $userSeasonPlayer = new self();
        $userSeasonPlayer->season = $userSeason;
        $userSeasonPlayer->user = $user;
        $userSeasonPlayer->driverId = $driverId;
        $userSeasonPlayer->points = 0;
        $userSeasonPlayer->position = 0;

        return $userSeasonPlayer;
    }

    public function addPoints(int $points): void
    {
        $this->points += $points;
    }

    public function updatePosition(int $position): void
    {
        $this->position = $position;
    }

    public function assignClassificationProperties(int $points, int $position): void
    {
        $this->points = $points;
        $this->position = $position;
    }

    public function getDriverPodiumsDTO(): DriverPodiumsDTO
    {
        $podiumsTable = DriverPodiumsDictionary::getPodiumsTable();

        /** @var UserSeasonRaceResult[] $raceResults */
        $raceResults = $this->getRaceResults()->toArray();

        foreach ($raceResults as $raceResult) {
            $position = $raceResult->getPosition();

            if ($position >= 1 && $position <= 3) {
                $podiumsTable[$position] += 1;
            }
        }

        return DriverPodiumsDTO::create(
            $podiumsTable[1],
            $podiumsTable[2],
            $podiumsTable[3],
        );
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     *
     * @return int[]
     */
    public static function getPlayersDriversIds(Collection $players): array
    {
        $driversIds = $players->map(function (UserSeasonPlayer $player) {
            return $player->getDriverId();
        });

        return $driversIds->toArray();
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     */
    public static function getPlayerByDriverId(Collection $players, int $driverId): ?UserSeasonPlayer
    {
        return $players->filter(function (UserSeasonPlayer $player) use ($driverId) {
            return $player->getDriverId() === $driverId;
        })->first();
    }
}
