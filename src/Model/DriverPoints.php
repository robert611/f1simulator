<?php 

namespace App\Model;

class DriverPoints 
{
    public object $raceResultsRepository;

    public function __construct($raceResultsRepository)
    {
        $this->raceResultsRepository = $raceResultsRepository;
    }

    public function getDriverPoints(int $driverId, object $season): int
    {
        $races = $season->getRaces();
        $driverResults = array();

        foreach($races as $race) {
            /* If for some reason(probably error, but in the future maybe dsn) driver does not have result in a race, then just skip it */
            if ($raceResult = $this->raceResultsRepository->findOneBy(['driver_id' => $driverId, 'race' => $race->getId()])) {
                $driverResults[] = $raceResult;
            }
        }
        
        $points = 0;

        $punctation = $this->getPunctation();

        foreach ($driverResults as $result) {
            $points += $punctation[$result->getPosition()];
        }

        return $points;
    }

    public function getDriverPointsByRace(int $driverId, object $race): int
    {
        if ($raceResult = $this->raceResultsRepository->findOneBy(['driver_id' => $driverId, 'race' => $race->getId()])) {
            $position = $raceResult->getPosition();
        } else {
            return 0;
        }

        return $this->getPunctation()[$position];
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