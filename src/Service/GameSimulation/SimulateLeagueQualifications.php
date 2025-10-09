<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Model\GameSimulation\LeagueQualificationResult;
use App\Model\GameSimulation\LeagueQualificationResultsCollection;

class SimulateLeagueQualifications
{
    public function __construct(
        private readonly QualificationsHelperService $helperService,
    ) {
    }

    public function getLeagueQualificationsResults(UserSeason $userSeason): LeagueQualificationResultsCollection
    {
        // @TODO, write tests
        $players = $userSeason->getPlayers();

        $drivers = UserSeasonPlayer::getPlayersDrivers($players);

        $result = LeagueQualificationResultsCollection::create();

        $driversInResults = [];

        $coupons = $this->helperService->generateCoupons();

        $totalDrivers = count($drivers);

        for ($position = 1; $position <= $totalDrivers; $position++) {
            // If both drivers from a given team are already drawn, repeat the draw until a team with < 2 finished drivers is picked
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $driversInResults));

            // Draw one of the remaining drivers from the selected team
            $driver = $this->drawDriverFromATeam($teamName, $drivers, $driversInResults);

            // If there is no driver (e.g. team not in league or all finished), retry this position
            if ($driver) {
                $userSeasonPlayer = UserSeasonPlayer::getPlayerByDriverId($players, $driver->getId());
                $qualificationResult = LeagueQualificationResult::create($userSeasonPlayer, $position);
                $result->addQualificationResult($qualificationResult);
                $driversInResults[] = $driver;
            } else {
                $position = $position - 1;
            }
        }

        return $result;
    }



    /**
     * @param Driver[] $leagueDrivers
     * @param Driver[] $results
     */
    public function drawDriverFromATeam(string $teamName, array $leagueDrivers, array $results): ?Driver
    {
        $teamDrivers = [];

        $normalizedTeamName = strtolower($teamName);

        foreach ($leagueDrivers as $driver) {
            if (strtolower($driver->getTeam()->getName()) === $normalizedTeamName) {
                $teamDrivers[] = $driver;
            }
        }

        if (count($teamDrivers) === 0) {
            return null;
        }

        $finishedDriverIds = [];
        foreach ($results as $finishedDriver) {
            $finishedDriverIds[$finishedDriver->getId()] = true;
        }

        $unfinishedDrivers = array_values(array_filter(
            $teamDrivers,
            static function (Driver $driver) use ($finishedDriverIds): bool {
                return !isset($finishedDriverIds[$driver->getId()]);
            },
        ));

        if (count($unfinishedDrivers) === 0) {
            return null;
        }

        return $unfinishedDrivers[array_rand($unfinishedDrivers)];
    }
}
