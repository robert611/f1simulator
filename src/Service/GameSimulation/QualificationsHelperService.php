<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Model\Configuration\TeamsStrength;

class QualificationsHelperService
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them greater */
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
