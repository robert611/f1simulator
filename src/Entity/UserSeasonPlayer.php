<?php

declare(strict_types=1);

namespace App\Entity;

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

    #[ORM\OneToMany(targetEntity: UserSeasonRaceResult::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $raceResults;

    #[ORM\OneToMany(targetEntity: UserSeasonQualification::class, mappedBy: 'player', orphanRemoval: true)]
    private Collection $qualificationsResults;

    public int $points;

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

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }
}
