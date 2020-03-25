<?php 

namespace App\Model;

use App\Entity\Race;
use App\Entity\RaceResults;

class DriverPoints 
{
    public object $doctrine;
    public object $raceResultsRepository;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->raceResultsRepository = $this->doctrine->getRepository(RaceResults::class);
    }

    public function getDriverPoints(int $driverId, object $season): int
    {
        $races = $season->getRaces();
        $driverResults = array();

        foreach($races as $race) {
            $driverResults[] = $this->raceResultsRepository->findOneBy(['driver_id' => $driverId, 'race' => $race->getId()]);
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
        $position = $this->raceResultsRepository->findOneBy(['race' => $race->getId(), 'driver_id' => $driverId])->getPosition();

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