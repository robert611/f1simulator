<?php

declare(strict_types=1);

namespace Domain\Contract\DTO;

use Domain\Entity\Team;

class TeamDTO
{
    private int $id;
    private string $name;
    private string $picture;

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

    public static function fromEntity(Team $team): self
    {
        $teamDTO = new self();
        $teamDTO->id = $team->getId();
        $teamDTO->name = $team->getName();
        $teamDTO->picture = $team->getPicture();

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
}
