<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Model\Configuration\TeamsStrength;
use App\Model\GameSimulation\LeagueQualificationResult;
use App\Model\GameSimulation\LeagueQualificationResultsCollection;

class SimulateLeagueQualifications
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them greater */
    public int $multiplier = 3;

    public function getLeagueQualificationsResults(UserSeason $userSeason): LeagueQualificationResultsCollection
    {
        // @TODO, write tests
        $players = $userSeason->getPlayers();

        $drivers = UserSeasonPlayer::getPlayersDrivers($players);

        $result = LeagueQualificationResultsCollection::create();

        $driversInResults = [];

        $coupons = $this->getCoupons();

        for ($i = 1, $j = count($drivers); $i <= $j; $i++) {
            /* If both drivers from given team are already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->checkIfBothDriversFromATeamAlreadyFinished($teamName, $driversInResults));

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
     * @return array<int, string>
     *
     * For instance [0 => "Mercedes", 1 => "Mercedes", 2 => "Red Bull"]
     */
    public function getCoupons(): array
    {
        $teams = TeamsStrength::getTeamsStrength();
        $coupons = [];

        for ($i = 1; $i <= $this->multiplier; $i++) {
            foreach ($teams as $team => $strength) {
                for ($j = 1; $j <= ceil($strength); $j++) {
                    $coupons[] = $team;
                }
            }
        }

        return $coupons;
    }

    /**
     * @param Driver[] $leagueDrivers
     * @param Driver[] $results
     */
    public function drawDriverFromATeam(string $teamName, array $leagueDrivers, array $results): ?Driver
    {
        $teamDrivers = [];

        foreach ($leagueDrivers as $driver) {
            if (strtolower($driver->getTeam()->getName()) === strtolower($teamName)) {
                $teamDrivers[] = $driver;
            }
        }

        shuffle($teamDrivers);

        if (count($teamDrivers) === 0) {
            return null;
        }

        if (count($teamDrivers) === 1) {
            if (false === in_array($teamDrivers[0], $results)) {
                return $teamDrivers[0];
            }

            return null;
        }

        $unFinishedTeamDrivers = [];

        if (false === in_array($teamDrivers[0], $results)) {
            $unFinishedTeamDrivers[] = $teamDrivers[0];
        }

        if (false === in_array($teamDrivers[1], $results)) {
            $unFinishedTeamDrivers[] = $teamDrivers[1];
        }

        if (count($unFinishedTeamDrivers) === 0) {
            return null;
        }

        return $unFinishedTeamDrivers[array_rand($unFinishedTeamDrivers)];
    }

    /**
     * @param Driver[] $results
     */
    public function checkIfBothDriversFromATeamAlreadyFinished(string $teamName, array $results): bool
    {
        $driversWhoFinished = 0;

        foreach ($results as $driver) {
            if (strtolower($driver->getTeam()->getName()) === strtolower($teamName)) {
                $driversWhoFinished++;
            }
        }

        if ($driversWhoFinished === 2) {
            return true;
        }

        return false;
    }
}
