<?php 

namespace App\Service\GameSimulation;

use App\Service\GameSimulation\SimulateQualifications;
use App\Service\GameSimulation\SimulateRaceService;

class SimulateLeagueRace
{
    public function __construct(
        private readonly SimulateQualifications $simulateQualifications,
    ) {
    }

    public function getRaceResults(object $players): array
    {
        /* Drivers who are replaced by players */
        $drivers = $this->getDrivers($players);

        $qualificationsResults = $this->simulateQualifications->getLeagueQualificationsResults($drivers);

        $raceResults = (new SimulateRaceService)->getLeagueRaceResults($drivers, $qualificationsResults);
    
        return [
            $this->setQualificationsResultsToPlayers($qualificationsResults, $players),
            $this->setRaceResultsToPlayers($raceResults, $players)
        ];
    }

    private function setQualificationsResultsToPlayers($qualificationsResults, $players): array
    {
        /* So now result holds a driver object, it will be change to player who is represented by given driver */
        foreach ($qualificationsResults as $key => $result) {
            $player = $players->filter(function($player) use ($result) {
                return $player->getDriver() == $result;
            })->first();
            $qualificationsResults[$key] = $player;
        }

       return $qualificationsResults;
    }

    private function setRaceResultsToPlayers($raceResults, $players): array
    {
        foreach ($raceResults as $key => $result) {
            $player = $players->filter(function($player) use ($result) {
                return $player->getDriver()->getId() == $result;
            })->first();
            $raceResults[$key] = $player;
        }

       return $raceResults;
    }

    private function getDrivers($players): array
    {
        $drivers = $players->map(function($player) {
            return $player->getDriver();
        });

        return [...$drivers->getValues()];
    }
}