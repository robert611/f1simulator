<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
#[ORM\Table(name: 'driver')]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    public int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
    public string $name;

    #[ORM\Column(name: 'surname', type: 'string', length: 255, nullable: false)]
    public string $surname;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'drivers')]
    #[ORM\JoinColumn(name: 'team_id', nullable: false)]
    public Team $team;

    #[ORM\Column(name: 'car_id', type: 'integer', nullable: false)]
    public int $carId;

    #[ORM\OneToMany(targetEntity: Qualification::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $qualifications;

    #[ORM\OneToMany(targetEntity: RaceResult::class, mappedBy: 'driver')]
    private Collection $raceResults;

    public int $points;

    public int $position;

    public function __construct()
    {
        $this->raceResults = new ArrayCollection();
        $this->qualifications = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->surname;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
    }

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function setCarId(int $carId): void
    {
        $this->carId = $carId;
    }

    public function getPoints(): int
    {
        return $this->points ?: 0;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function getPosition(): int
    {
        return $this->position ?: 0;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Collection<Qualification>
     */
    public function getQualifications(): Collection
    {
        return $this->qualifications;
    }

    public function addQualification(Qualification $qualification): self
    {
        if (!$this->qualifications->contains($qualification)) {
            $this->qualifications[] = $qualification;
            $qualification->setDriver($this);
        }

        return $this;
    }

    public function removeQualification(Qualification $qualification): self
    {
        if ($this->qualifications->contains($qualification)) {
            $this->qualifications->removeElement($qualification);
        }

        return $this;
    }

    /**
     * @return Collection<RaceResult>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(RaceResult $raceResult): self
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setDriver($this);
        }

        return $this;
    }

    public function removeRaceResult(RaceResult $raceResult): self
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }

        return $this;
    }
}
