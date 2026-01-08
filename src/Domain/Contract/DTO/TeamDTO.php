<?php

declare(strict_types=1);

namespace Domain\Contract\DTO;

use Domain\Entity\Driver;
use Domain\Entity\Team;

class TeamDTO
{
    private int $id;
    private string $name;
    private string $picture;

    /** @var Driver[] */
    private array $drivers;
    private string $highResolutionPicture;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * @return DriverDTO[]
     */
    public function getDrivers(): array
    {
        return DriverDTO::fromEntityCollection($this->drivers);
    }

    public function getHighResolutionPicture(): string
    {
        return $this->highResolutionPicture;
    }

    public static function fromEntity(Team $team): self
    {
        $teamDTO = new self();
        $teamDTO->id = $team->getId();
        $teamDTO->name = $team->getName();
        $teamDTO->picture = $team->getPicture();
        $teamDTO->drivers = $team->getDrivers()->toArray();
        $teamDTO->highResolutionPicture = $team->getHighResolutionPicture();

        return $teamDTO;
    }

    /**
     * @param $teams Team[]
     *
     * @return TeamDTO[]
     */
    public static function fromEntityCollection(array $teams): array
    {
        $teamsDTO = [];

        foreach ($teams as $team) {
            $teamsDTO[] = TeamDTO::fromEntity($team);
        }

        return $teamsDTO;
    }

    public function drawDriverToReplace(): ?DriverDTO
    {
        $driver = Team::drawDriverToReplaceMethod($this->drivers);

        if (null === $driver) {
            return null;
        }

        return DriverDTO::fromEntity($driver);
    }
}
