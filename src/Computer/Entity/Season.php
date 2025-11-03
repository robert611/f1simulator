<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Domain\Contract\Model\DriverPodiumsDictionary;
use Domain\Contract\Model\DriverPodiumsDTO;
use Domain\Entity\Driver;
use Security\Entity\User;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Table(name: 'season')]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(name: 'driver_id', nullable: false)]
    private Driver $driver;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'seasons')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false)]
    private User $user;

    #[ORM\Column(name: 'completed', type: 'boolean', nullable: false)]
    private bool $completed;

    #[ORM\OneToMany(targetEntity: Race::class, mappedBy: 'season')]
    private Collection $races;

    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @return Collection<Race>
     */
    public function getRaces(): Collection
    {
        return $this->races;
    }

    public function getLastRace(): ?Race
    {
        return $this->races->last();
    }

    public function addRaces(Race $races): void
    {
        if (!$this->races->contains($races)) {
            $this->races[] = $races;
            $races->setSeason($this);
        }
    }

    public function removeRaces(Race $races): void
    {
        if ($this->races->contains($races)) {
            $this->races->removeElement($races);
        }
    }

    public function addRace(Race $race): void
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
            $race->setSeason($this);
        }
    }

    public function removeRace(Race $race): void
    {
        if ($this->races->contains($race)) {
            $this->races->removeElement($race);
        }
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public static function create(User $user, Driver $driver): self
    {
        $season = new self();
        $season->user = $user;
        $season->driver = $driver;
        $season->completed = false;

        return $season;
    }

    public function endSeason(): void
    {
        $this->completed = true;
    }

    public function getDriverPodiumsDTO(): DriverPodiumsDTO
    {
        $races = $this->getRaces();

        $podiumsTable = DriverPodiumsDictionary::getPodiumsTable();

        $driverId = $this->driver->getId();

        foreach ($races as $race) {
            $raceResultCollection = $race->getRaceResults()->filter(function (RaceResult $result) use ($driverId) {
                return $result->getDriverId() === $driverId;
            });

            if ($raceResultCollection->isEmpty()) {
                continue;
            }

            $position = $raceResultCollection->first()->getPosition();

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
}
