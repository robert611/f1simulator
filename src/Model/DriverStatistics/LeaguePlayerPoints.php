<?php 

namespace App\Model\DriverStatistics;

use App\Model\Configuration\RacePunctation;

class LeaguePlayerPoints
{
    public function getPlayerPoints(object $player): int
    {
        $points = 0;

        $player->getRaceResults()->map(function ($result) use (&$points) {
            $points += $this->getPlayerPointsByResult($result);
        });

        return $points;
    }

    public function getPlayerPointsByResult(object $result): int
    {
        return (new RacePunctation)->getPunctation()[$result->getPosition()];
    }
}