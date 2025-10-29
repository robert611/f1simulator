<?php

declare(strict_types=1);

namespace Multiplayer\Model;

use Domain\Contract\DTO\TeamDTO;

class TeamLeagueResult
{
    private TeamDTO $team;
    private int $points;
    private int $position;

    public function getTeam(): TeamDTO
    {
        return $this->team;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public static function create(TeamDTO $team, int $points, int $position): self
    {
        $teamSeasonResult = new self();
        $teamSeasonResult->team = $team;
        $teamSeasonResult->points = $points;
        $teamSeasonResult->position = $position;

        return $teamSeasonResult;
    }
}
