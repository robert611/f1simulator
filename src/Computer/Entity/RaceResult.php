<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\RaceResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Domain\Entity\Driver;

#[ORM\Entity(repositoryClass: RaceResultRepository::class)]
#[ORM\Table(name: 'race_result')]
class RaceResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false)]
    private int $position;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'raceResults')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private Race $race;

    #[ORM\Column(name: 'driver_id', type: 'integer', nullable: false)]
    private int $driverId;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function setDriverId(int $driverId): void
    {
        $this->driverId = $driverId;
    }

    public static function create(int $position, Race $race, int $driverId): self
    {
        $raceResult = new self();
        $raceResult->position = $position;
        $raceResult->race = $race;
        $raceResult->driverId = $driverId;

        return $raceResult;
    }
}
