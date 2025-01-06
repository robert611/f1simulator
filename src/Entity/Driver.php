<?php

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
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    public string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    public string $surname;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'drivers')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Team $team;

    #[ORM\Column(type: 'integer')]
    public int $car_id;

    public $points;

    public $position;

    #[ORM\OneToMany(targetEntity: Qualification::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $qualifications;

    #[ORM\OneToMany(targetEntity: RaceResults::class, mappedBy: 'driver')]
    private Collection $raceResults;

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

    public function getTeam(): ?team
    {
        return $this->team;
    }

    public function setTeam(?team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getCarId(): ?int
    {
        return $this->car_id;
    }

    public function setCarId(int $car_id): self
    {
        $this->car_id = $car_id;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points ? $this->points : 0;
    }

    public function setPoints(string $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position ?: 0;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection|Qualification[]
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
            // set the owning side to null (unless already changed)
            if ($qualification->getDriver() === $this) {
                $qualification->setDriver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RaceResults[]
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
            // set the owning side to null (unless already changed)
            if ($raceResult->getDriver() === $this) {
                $raceResult->setDriver(null);
            }
        }

        return $this;
    }
}
