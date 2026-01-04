<?php

declare(strict_types=1);

namespace Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\Repository\DriverRepository;

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

    #[ORM\Column(name: 'car_number', type: 'integer', unique: true, nullable: false)]
    public int $carNumber;

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

    public static function create(string $name, string $surname, Team $team, int $carNumber): self
    {
        $driver = new self();
        $driver->name = $name;
        $driver->surname = $surname;
        $driver->team = $team;
        $driver->carNumber = $carNumber;

        return $driver;
    }

    public function update(string $name, string $surname, Team $team, int $carNumber): void
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->team = $team;
        $this->carNumber = $carNumber;
    }
}
