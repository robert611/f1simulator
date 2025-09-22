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

        $qualificationsResults = $this->simulateQualifications->getLeagueQualificationsResults($userSeason);

        $raceResults = $this->simulateRaceService->getLeagueRaceResults($drivers, $qualificationsResults);

        $preparedRaceResults = $this->setRaceResultsToPlayers($raceResults, $players);

        return LeagueRaceResultsDTO::create($qualificationsResults, $preparedRaceResults);
    }

    /**
     * @param int[] $raceResults
     * @param Collection<UserSeasonPlayer> $players
     *
     * @return UserSeasonPlayer[]
     */
    private function setRaceResultsToPlayers(array $raceResults, Collection $players): array
    {
        foreach ($raceResults as $key => $driverId) {
            $player = UserSeasonPlayer::getPlayerByDriverId($players, $driverId);
            $raceResults[$key] = $player;
        }

        return $raceResults;
    }
}
