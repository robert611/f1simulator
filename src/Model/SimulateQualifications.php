<?php 

namespace App\Model;

class SimulateQualifications
{
    /* Every team has it's strength which says how competetive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them greater */
    public int $multiplier = 3;

    public function getQualificationsResults($drivers)
    {
        $results = array();

        $coupons = $this->getCoupons();

        for ($i = 1; $i <= count($drivers); $i++) {
            /* If boths driver from given team will be already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do
            {
                $teamName = $coupons[rand(1, count($coupons))];
            } 
            while($this->checkIfBothDriversFromTeamAlreadyFinished($teamName, $results));

            /* At this point team from which driver will be draw is drawn, not the driver per se so now draw one of the drivers from that team and put him in finished drivers */
            $driver = $this->drawDriverFromTeam($teamName, $drivers, $results);

            $results[$i] = $driver;
        }

        return $results;
    }

    public function getCoupons()
    {
        $teams = $this->getTeamsStrength();
        $coupons = array();
        
        for ($i = 1; $i <= $this->multiplier; $i++)
        {
            foreach ($teams as $team => $strength) {
                $lastIndex = count($coupons);

                for ($j = 1; $j <= ceil($strength); $j++) {
                    $coupons[$lastIndex + $j] = $team;
                }
            }
        }
        
        return $coupons;
    }   

    public function drawDriverFromTeam($teamName, $drivers, $results)
    {
        $teamDrivers = array();

        shuffle($drivers);

        /* Get drivers from given team */
        foreach ($drivers as $key => $driver) {
            if($driver->getTeam()->getName() == $teamName) $teamDrivers[] = $driver;
        }
       
        /* If one of the drivers already finished race then return the second one */
        if (in_array($teamDrivers[0], $results)) {
            return $teamDrivers[1];
        } else if (in_array($teamDrivers[1], $results)) {
            return $teamDrivers[0];
        }

        /* If none of the drivers finished then draw one of them */
        return $teamDrivers[rand(0, 1)];
    }

    public function checkIfBothDriversFromTeamAlreadyFinished(string $teamName, array $results): bool
    {
        $driversWhoFinished = 0;

        foreach ($results as $driver) {
            if($driver->getTeam()->getName() == $teamName) $driversWhoFinished++;
        }

        if ($driversWhoFinished == 2) return true;

        return false;
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