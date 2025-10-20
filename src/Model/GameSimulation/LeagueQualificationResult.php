<?php

declare(strict_types=1);

namespace App\Model\GameSimulation;

use Multiplayer\Entity\UserSeasonPlayer;

class LeagueQualificationResult
{
    private UserSeasonPlayer $userSeasonPlayer;

    private int $position;

    public function getUserSeasonPlayer(): UserSeasonPlayer
    {
        return $this->userSeasonPlayer;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public static function create(UserSeasonPlayer $userSeasonPlayer, int $position): self
    {
        $leagueQualificationResult = new self();
        $leagueQualificationResult->userSeasonPlayer = $userSeasonPlayer;
        $leagueQualificationResult->position = $position;

        return $leagueQualificationResult;
    }
}
