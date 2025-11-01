<?php

declare(strict_types=1);

namespace Domain\Contract\GameSimulation;

use Domain\Contract\DTO\DriverDTO;
use Domain\Model\Configuration\TeamsStrength;

class QualificationsHelperService
{
    // Used to multiply differences between teams
    public int $multiplier = 3;

    /**
     * @return array<int, string>
     *
     * For instance [0 => "Mercedes", 1 => "Mercedes", 2 => "Red Bull"]
     */
    public function generateCoupons(): array
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
     * @param DriverDTO[] $results
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

    /**
     * @param DriverDTO[] $drivers
     * @param DriverDTO[] $results
     */
    public function drawDriverFromATeam(string $teamName, array $drivers, array $results): ?DriverDTO
    {
        $teamDrivers = [];

        $normalizedTeamName = strtolower($teamName);

        /* Get drivers from a given team */
        foreach ($drivers as $driver) {
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
            static function (DriverDTO $driver) use ($finishedDriverIds): bool {
                return !isset($finishedDriverIds[$driver->getId()]);
            },
        ));

        if (count($unfinishedDrivers) === 0) {
            return null;
        }

        return $unfinishedDrivers[array_rand($unfinishedDrivers)];
    }
}
