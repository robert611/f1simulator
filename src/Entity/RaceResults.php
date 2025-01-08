<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RaceResultsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceResultsRepository::class)]
class RaceResults
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false)]
    private int $position;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private Race $race;

    #[ORM\ManyToOne(targetEntity: Driver::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(name: 'driver_id', nullable: false)]
    private Driver $driver;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function setDriver(Driver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }
}
