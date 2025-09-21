<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Entity\UserSeason;
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

    public function simulateRaceResults(UserSeason $userSeason): LeagueRaceResultsDTO
    {
        $players = $userSeason->getPlayers();

        $drivers = UserSeasonPlayer::getPlayersDrivers($players);

        $qualificationsResults = $this->simulateQualifications->getLeagueQualificationsResults($drivers);

        $raceResults = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationsResults);

        $preparedQualificationsResults = $this->setQualificationsResultsToPlayers($qualificationsResults, $players);

        $preparedRaceResults = $this->setRaceResultsToPlayers($raceResults, $players);

        return LeagueRaceResultsDTO::create($preparedQualificationsResults, $preparedRaceResults);
    }

    /**
     * @param Driver[] $qualificationsResults
     * @param Collection<UserSeasonPlayer> $players
     */
    private function setQualificationsResultsToPlayers(array $qualificationsResults, Collection $players): array
    {
        foreach ($qualificationsResults as $key => $driver) {
            $player = $players->filter(function (UserSeasonPlayer $player) use ($driver) {
                return $player->getDriver()->getId() === $driver->getId();
            })->first();
            $qualificationsResults[$key] = $player;
        }

        return $qualificationsResults;
    }

    /**
     * @param int[] $raceResults
     * @param Collection<UserSeasonPlayer> $players
     */
    private function setRaceResultsToPlayers(array $raceResults, Collection $players): array
    {
        foreach ($raceResults as $key => $driverId) {
            $player = $players->filter(function (UserSeasonPlayer $player) use ($driverId) {
                return $player->getDriver()->getId() === $driverId;
            })->first();
            $raceResults[$key] = $player;
        }

        return $raceResults;
    }
}
