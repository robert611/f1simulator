<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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

    private int $userPoints;
   
    public function __construct()
    {
        $this->races = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUserPoints(int $points): void
    {
        $this->userPoints = $points;
    }

    public function getUserPoints(): int
    {
        return $this->userPoints;
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

    public function addRace(race $race): void
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
            $race->setSeason($this);
        }
    }

    public function removeRace(race $race): void
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
}
