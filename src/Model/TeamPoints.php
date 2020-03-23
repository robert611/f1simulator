<?php 

namespace App\Model;

use App\Repositories\DriversRepository;
use App\Models\DriverPoints;

class TeamPoints 
{
    public function getTeamPoints($teamId, $seasonId)
    {
        $teamDrivers = (new DriversRepository)->findBy(['team_id' => $teamId]);
        $points = 0;

        foreach ($teamDrivers as $driver)
        {
            $points += (new DriverPoints)->getDriverPoints($driver['id'], $seasonId);
        }

        return $points;
    }
}