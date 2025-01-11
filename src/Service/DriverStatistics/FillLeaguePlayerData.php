<?php 

namespace App\Service\DriverStatistics;

use App\Service\DriverStatistics\LeaguePlayerPoints;
use App\Service\DriverStatistics\DriverPodiums;

class FillLeaguePlayerData
{
    private object $player;
    private object $league;

    public function __construct(object $player, object $league)
    {
        $this->player = $player;
        $this->league = $league;
    }

    public function getPlayer()
    {
        $this->player->podiums = (new DriverPodiums)->getDriverPodiums($this->player, $this->league);
        $this->player->points = (new LeaguePlayerPoints)->getPlayerPoints($this->player);
    
        return $this->player;
    }
}