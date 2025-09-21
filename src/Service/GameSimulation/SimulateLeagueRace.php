<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Entity\UserSeasonPlayer;
use App\Model\GameSimulation\LeagueRaceResultsDTO;
use Doctrine\Common\Collections\Collection;

class SimulateLeagueRace
{
    public function __construct(
        private readonly SimulateQualifications $simulateQualifications,
        private readonly SimulateRaceService $simulateRaceService,
    ) {
    }

    /**
     * @param Collection<UserSeasonPlayer> $players
     */
    public function getRaceResults(Collection $players): LeagueRaceResultsDTO
    {
        $drivers = UserSeasonPlayer::getPlayersDrivers($players);

        $qualificationsResults = $this->simulateQualifications->getLeagueQualificationsResults($drivers);

        $raceResults = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationsResults);

        $preparedQualificationsResults = $this->setQualificationsResultsToPlayers($qualificationsResults, $players);

        $preparedRaceResults = $this->setRaceResultsToPlayers($raceResults, $players);

        return LeagueRaceResultsDTO::create($preparedQualificationsResults, $preparedRaceResults);
    }

    private function setQualificationsResultsToPlayers($qualificationsResults, $players): array
    {
        /* So now result holds a driver object, it will be change to player who is represented by given driver */
        foreach ($qualificationsResults as $key => $result) {
            $player = $players->filter(function ($player) use ($result) {
                return $player->getDriver() == $result;
            })->first();
            $qualificationsResults[$key] = $player;
        }

        return $qualificationsResults;
    }

    private function setRaceResultsToPlayers($raceResults, $players): array
    {
        foreach ($raceResults as $key => $result) {
            $player = $players->filter(function ($player) use ($result) {
                return $player->getDriver()->getId() == $result;
            })->first();
            $raceResults[$key] = $player;
        }

        return $raceResults;
    }
}
