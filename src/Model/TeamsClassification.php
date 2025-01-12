<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Team;

class TeamsClassification
{
    private int $position;
    private Team $team;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public static function __create(): void
    {

    }
}
