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

        for ($i = 1, $j = count($drivers); $i <= $j; $i++) {
            /* If both drivers from given team are already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $driversInResults));

            /* At this point team from which a driver will be draw is drawn, not the driver per se so now draw one of the drivers from that team and put him in finished drivers */
            $driver = $this->drawDriverFromATeam($teamName, $drivers, $driversInResults);

            /* If there is no drawn driver, then iterate once again */
            if ($driver) {
                $userSeasonPlayer = UserSeasonPlayer::getPlayerByDriverId($players, $driver->getId());
                $qualificationResult = LeagueQualificationResult::create($userSeasonPlayer, $i);
                $result->addQualificationResult($qualificationResult);
                $driversInResults[] = $driver;
            } else {
                $i = $i - 1;
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
