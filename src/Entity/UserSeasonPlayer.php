<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\DriverPodiumsDictionary;
use App\Model\DriverPodiumsDTO;
use App\Repository\UserSeasonPlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userSeasonPlayers')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(name: 'driver_id', nullable: false)]
    private Driver $driver;

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

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
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

    public static function create(UserSeason $userSeason, User $user, Driver $driver): self
    {
        $userSeasonPlayer = new self();
        $userSeasonPlayer->season = $userSeason;
        $userSeasonPlayer->user = $user;
        $userSeasonPlayer->driver = $driver;
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
     * @return Driver[]
     */
    public static function getPlayersDrivers(Collection $players): array
    {
        $drivers = $players->map(function (UserSeasonPlayer $player) {
            return $player->getDriver();
        });

        return $drivers->toArray();
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     */
    public static function getPlayerByDriverId(Collection $players, int $driverId): ?UserSeasonPlayer
    {
        return $players->filter(function (UserSeasonPlayer $player) use ($driverId) {
            return $player->getDriver()->getId() === $driverId;
        })->first();
    }
}
