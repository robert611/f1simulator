<?php

declare(strict_types=1);

namespace Domain\Contract\DTO;

use Domain\Entity\Driver;

class DriverDTO
{
    private int $id;
    private string $name;
    private string $surname;
    private TeamDTO $teamDTO;
    private int $carNumber;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTeamDTO(): TeamDTO
    {
        return $this->teamDTO;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCarNumber(): int
    {
        return $this->carNumber;
    }

    public static function fromEntity(Driver $driver): self
    {
        $driverDTO = new self();
        $driverDTO->id = $driver->getId();
        $driverDTO->name = $driver->getName();
        $driverDTO->surname = $driver->getSurname();
        $driverDTO->teamDTO = TeamDTO::fromEntity($driver->getTeam());
        $driverDTO->carNumber = $driver->getCarNumber();

        return $driverDTO;
    }
}
