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

    #[ORM\Column(name: 'car_number', type: 'integer', nullable: false)]
    public int $carNumber;

    #[ORM\OneToMany(targetEntity: Qualification::class, mappedBy: 'driver', orphanRemoval: true)]
    private Collection $qualifications;

    #[ORM\OneToMany(targetEntity: RaceResult::class, mappedBy: 'driver')]
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

    public function getCarNumber(): int
    {
        return $this->carNumber;
    }

    public function setCarNumber(int $carNumber): void
    {
        $this->carNumber = $carNumber;
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

    public static function create(string $name, string $surname, Team $team, int $carNumber): self
    {
        $driver = new self();
        $driver->name = $name;
        $driver->surname = $surname;
        $driver->team = $team;
        $driver->carNumber = $carNumber;

        return $driver;
    }
}
