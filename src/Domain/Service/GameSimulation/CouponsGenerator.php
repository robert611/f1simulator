<?php

declare(strict_types=1);

namespace Domain\Service\GameSimulation;

use Domain\Model\Configuration\QualificationAdvantage;
use Domain\Model\Configuration\TeamsStrength;
use Domain\Entity\Driver;

class CouponsGenerator
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies the strength of the teams
     by some value to make differences between them grater */
    public int $multiplier = 3;

    /**
     * @param Driver[] $qualificationsResults
     *
     * @return int[]
     */
    public function generateCoupons(array $qualificationsResults): array
    {
        $teams = TeamsStrength::getTeamsStrength();
        $qualificationResultAdvantage = QualificationAdvantage::getQualificationResultAdvantage();

        $coupons = [];

        // Calculate driver strength and create weighted coupons directly
        foreach ($qualificationsResults as $position => $driver) {
            $driverTeamStrength = $teams[$driver->getTeam()->getName()];
            $driverQualificationAdvantage = $qualificationResultAdvantage[$position];
            $strength = ceil($driverTeamStrength + $driverQualificationAdvantage);

            // Add driver ID to coupons based on their strength, repeated for multiplier
            for ($i = 0; $i < $this->multiplier; $i++) {
                for ($j = 0; $j < $strength; $j++) {
                    $coupons[] = $driver->getId();
                }
            }
        }

        return $coupons;
    }
}
