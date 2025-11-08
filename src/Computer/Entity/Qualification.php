<?php

declare(strict_types=1);

namespace Computer\Entity;

use Computer\Repository\QualificationRepository;
use Doctrine\ORM\Mapping as ORM;

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

    #[ORM\Column(name: 'driver_id', type: 'integer', nullable: false)]
    private int $driverId;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'qualifications')]
    #[ORM\JoinColumn(name: 'race_id', nullable: false)]
    private Race $race;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDriverId(): int
    {
        return $this->driverId;
    }

    public function setDriverId(int $driverId): void
    {
        $this->driverId = $driverId;
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

    public static function create(int $driverId, Race $race, int $position): self
    {
        $qualification = new self();
        $qualification->driverId = $driverId;
        $qualification->race = $race;
        $qualification->position = $position;

        return $qualification;
    }
}
