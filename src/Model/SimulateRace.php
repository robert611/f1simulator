<?php 

namespace App\Model;

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
                $driverId = $coupons[rand(1, count($coupons))];
            } 
            while(in_array($driverId, $results));

            $results[$i] = $driverId;
        }

        return $results;
    }

    public function getCoupons(array $qualificationsResults): array
    {
        $teams = $this->getTeamsStrength();
        $qualificationResultAdvantage = $this->getQualificationResultAdvantage();

        $coupons = array();

        for ($i = 1; $i <= $this->multiplier; $i++)
        {
            foreach ($qualificationsResults as $position => $driver) {
                $lastIndex = count($coupons);

                $driverTeamStrength = $teams[$driver->getTeam()->getName()];
                $driverQualificationAdvantage = $qualificationResultAdvantage[$position];

                $strength = ceil($driverTeamStrength + $driverQualificationAdvantage);

                for ($j = 1; $j <= $strength; $j++) {
                    $coupons[$lastIndex + $j] = $driver->getId();
                }
            }
        }
        
        return $coupons;
    }

    public function getQualificationResultAdvantage()
    {
        return [
            1 => 10,
            2 => 9,
            3 => 8,
            4 => 7,
            5 => 6,
            6 => 6,
            7 => 5,
            8 => 5,
            9 => 5,
            10 => 4,
            11 => 4,
            12 => 4,
            13 => 4,
            14 => 3,
            15 => 3,
            16 => 3,
            17 => 2,
            18 => 1,
            19 => 1,
            20 => 1
        ];
    }

    public function getTeamsStrength()
    {
        return [
            'Mercedes' => 23,
            'Ferrari' => 19.7,
            'Red Bull' => 19.6,
            'Mclaren' => 6.4,
            'Renault' => 6.2,
            'Racing Point' => 6.1,
            'Toro Rosso' => 5.9,
            'Haas' => 5.7,
            'Alfa Romeo' => 5.7,
            'Williams' => 0.6
        ];
    }
}