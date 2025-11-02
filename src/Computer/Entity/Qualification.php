<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\QualificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Domain\Entity\Driver;

#[ORM\Entity(repositoryClass: QualificationRepository::class)]
#[ORM\Table(name: 'qualification')]
class Qualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    private int $id;

    #[ORM\Column(name: 'position', type: 'smallint', nullable: false)]
    private int $position;

    #[ORM\ManyToOne(targetEntity: Driver::class)]
    #[ORM\JoinColumn(name: 'driver_id', nullable: false)]
    private Driver $driver;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'qualifications')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private Race $race;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public static function create(Driver $driver, Race $race, int $position): self
    {
        $qualification = new self();
        $qualification->driver = $driver;
        $qualification->race = $race;
        $qualification->position = $position;

        return $qualification;
    }
}
