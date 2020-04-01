<?php 

namespace App\Model;

use App\Model\DriverPoints;

class TeamPoints 
{
    public function getTeamPoints($team, $season)
    {
        $teamDrivers = $team->getDrivers();
        $points = 0;

        foreach ($teamDrivers as $driver)
        {
            $points += (new DriverPoints())->getDriverPoints($driver, $season);
        }

        return $points;
    }
}