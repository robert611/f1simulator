<?php

declare(strict_types=1);

namespace App\Model;

use Domain\Entity\Team;

class TeamSeasonResult
{
    private Team $team;
    private int $points;
    private int $position;

    public function getTeam(): Team
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

    public static function create(Team $team, int $points, int $position): self
    {
        $teamSeasonResult = new self();
        $teamSeasonResult->team = $team;
        $teamSeasonResult->points = $points;
        $teamSeasonResult->position = $position;

        return $teamSeasonResult;
    }
}
