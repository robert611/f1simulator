<?php 

namespace App\Model;

use App\Entity\RaceResults;

class DriverPodiums
{
    public object $doctrine;
    public object $raceResultsRepository;
    public object $raceRepository;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->raceResultsRepository = $this->doctrine->getRepository(RaceResults::class);
    }

    public function getDriverPodiums($driver, $season)
    {
        $races = $season->getRaces();

        $podiumsTable = $this->getPodiumsTable();

        foreach ($races as $race) {
            $position = $this->raceResultsRepository->findOneBy(['race' => $race->getId(), 'driver_id' => $driver->getId()])->getPosition();

            if ($position >= 1 && $position <= 3)  $podiumsTable[$position] += 1;
        }

        return $podiumsTable;
    }

    public function getPodiumsTable() {
        return [
            1 => 0,
            2 => 0,
            3 => 0
        ];
    }
}