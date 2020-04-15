<?php 

namespace App\Model\DriverStatistics;

use App\Model\Configuration\RacePunctation;

class LeaguePlayerPoints
{
    public function getPlayerPoints(object $player)
    {
        $points = 0;

        $player->getRaceResults()->map(function ($result) use (&$points) {
            $points += (new RacePunctation)->getPunctation()[$result->getPosition()];
        });

        return $points;
    }

    public function getPlayerPointsByRace(object $player)
    {
        return (new RacePunctation)->getPunctation()[$player->getPosition()];
    }
}