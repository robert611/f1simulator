<?php

declare(strict_types=1);

namespace App\Service\DriverStatistics;

class FillLeaguePlayerData
{
    private object $player;

    public function __construct(object $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): object
    {
        $this->player->points = (new LeaguePlayerPoints())->getPlayerPoints($this->player);

        return $this->player;
    }
}
