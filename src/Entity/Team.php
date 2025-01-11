<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'team')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer', nullable: false)]
    public int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 64, nullable: false)]
    public string $name;

    #[ORM\Column(name: 'picture', type: 'string', length: 64, nullable: false)]
    public string $picture;

    #[ORM\OneToMany(targetEntity: Driver::class, mappedBy: 'team', orphanRemoval: true)]
    public Collection $drivers;

    public int $points;

    public int $position;

    public array $players = [];

    public function __construct()
    {
        $this->drivers = new ArrayCollection();
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

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @return Collection<Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): void
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers[] = $driver;
            $driver->setTeam($this);
        }
    }

    public function removeDriver(Driver $driver): void
    {
        if ($this->drivers->contains($driver)) {
            $this->drivers->removeElement($driver);
        }
    }

    /**
     * @return UserSeasonPlayer[]
     * There is no UserSeasonPlayers column in database
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    public function addPlayer(UserSeasonPlayer $player): void
    {
        foreach ($this->players as $existingPlayer) {
            if ($existingPlayer->getId() === $player->getId()) {
                return;
            }
        }

        $this->players[] = $player;
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
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
