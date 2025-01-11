<?php 

namespace App\Service\GameSimulation;

use App\Service\Configuration\TeamsStrength;
use App\Service\Configuration\QualificationAdvantage;

class SimulateRace
{
    /* Every team has it's strength which says how competetive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them grater */
    public int $multiplier = 3;

    public function getRaceResults($drivers, $qualificationsResults): array
    {
        $results = array();

        $coupons = $this->getCoupons($qualificationsResults);

        for ($i = 1; $i <= count($drivers); $i++) {
            do
            {
                $driverId = $coupons[rand(0, count($coupons) - 1)];
            } 
            while(in_array($driverId, $results));

            $results[$i] = $driverId;
        }

        return $results;
    }

    public function getCoupons(array $qualificationsResults): array
    {
        $teams = (new TeamsStrength)->getTeamsStrength();
        $qualificationResultAdvantage = (new QualificationAdvantage)->getQualificationResultAdvantage();

        $coupons = array();
        $driversStrength = array();

        /* Calculate Strength Of Drivers */
        foreach ($qualificationsResults as $position => $driver) {
            $driverTeamStrength = $teams[$driver->getTeam()->getName()];
            $driverQualificationAdvantage = $qualificationResultAdvantage[$position];

            $strength = ceil($driverTeamStrength + $driverQualificationAdvantage);

            $driversStrength[$driver->getId()] = $strength;
        }

        /* Mercedes is the strongest team, and first index contains the driver who won qualifications */
        $highestPossibleStrength = ceil($teams['Mercedes'] + $qualificationResultAdvantage[1]);

        for ($i = 1; $i <= $this->multiplier; $i++)
        {
            for ($j = 1; $j <= $highestPossibleStrength; $j++)
            {
                foreach ($driversStrength as $driverId => $driverStrength) {
                    if ($j <= $driverStrength) {
                        $coupons[] = $driverId;
                    }
                }
            }
        }
    
        return $coupons;
    }
}