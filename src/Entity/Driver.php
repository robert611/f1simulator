<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
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

    #[ORM\OneToMany(targetEntity: RaceResults::class, mappedBy: 'driver')]
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getCarId(): int
    {
        return $this->carId;
    }

    public function setCarId(int $carId): self
    {
        $this->carId = $carId;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points ?: 0;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position ?: 0;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
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
     * @return Collection<RaceResults>
     */
    public function getRaceResults(): Collection
    {
        return $this->raceResults;
    }

    public function addRaceResult(RaceResults $raceResult): self
    {
        if (!$this->raceResults->contains($raceResult)) {
            $this->raceResults[] = $raceResult;
            $raceResult->setDriver($this);
        }

        return $this;
    }

    public function removeRaceResult(RaceResults $raceResult): self
    {
        if ($this->raceResults->contains($raceResult)) {
            $this->raceResults->removeElement($raceResult);
        }

        return $this;
    }
}
