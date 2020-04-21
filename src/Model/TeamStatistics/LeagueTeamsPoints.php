<?php 

namespace App\Model\TeamStatistics;

use App\Model\DriverStatistics\LeaguePlayerPoints;

class LeagueTeamsPoints 
{
    public function getTeamsPoints(object $teams)
    {
        $teams->map(function($team) {
            $points = 0;

            $team->getPlayers()->map(function($player) use (&$points) {
                $points += (new LeaguePlayerPoints)->getPlayerPoints($player);
            });

            $team->setPoints($points);
        });

        return $teams;
    }
}