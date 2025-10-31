<?php

declare(strict_types=1);

namespace Multiplayer\Model\GameSimulation;

use Domain\Contract\DTO\DriverDTO;
use Multiplayer\Entity\UserSeasonPlayer;

class LeagueQualificationResult
{
    private UserSeasonPlayer $userSeasonPlayer;

    private DriverDTO $driver;

    private int $position;

    public function getUserSeasonPlayer(): UserSeasonPlayer
    {
        return $this->userSeasonPlayer;
    }

    public function getDriver(): DriverDTO
    {
        return $this->driver;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public static function create(UserSeasonPlayer $userSeasonPlayer, DriverDTO $driver, int $position): self
    {
        $leagueQualificationResult = new self();
        $leagueQualificationResult->userSeasonPlayer = $userSeasonPlayer;
        $leagueQualificationResult->driver = $driver;
        $leagueQualificationResult->position = $position;

        return $leagueQualificationResult;
    }
}
