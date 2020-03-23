<?php 

namespace App\Model;

use App\Repositories\RaceResultsRepository;
use App\Repositories\RacesRepository;

class DriverPoints 
{
    public function getDriverPoints(int $driverId, int $seasonId): int
    {
        $races = (new RacesRepository)->findBy(['season_id' => $seasonId]);
        $driverResults = array();

        foreach($races as $race) {
            $driverResults = array_merge($driverResults, (new RaceResultsRepository)->findBy(['driver_id' => $driverId, 'race_id' => $race['id']]));
        }
        
        $points = 0;

        $punctation = $this->getPunctation();

        foreach ($driverResults as $result) {
            $points += $punctation[$result['position']];
        }

        return $points;
    }

    public function getPunctation()
    {
        return [
            '1' => 25,
            '2' => 18, 
            '3' => 15,
            '4' => 12,
            '5' => 10,
            '6' => 8,
            '7' => 6,
            '8' => 4,
            '9' => 2,
            '10' => 1,
            '11' => 0,
            '12' => 0,
            '13' => 0,
            '14' => 0,
            '15' => 0,
            '16' => 0,
            '17' => 0,
            '18' => 0, 
            '19' => 0,
            '20' => 0
        ];
    }
}