<?php 

namespace App\Model;

use App\Repositories\RaceResultsRepository;
use App\Repositories\RacesRepository;
use App\Repositories\DriversRepository;
use App\Repositories\TeamsRepository;

class SimulateRace
{
    /* Every team has it's strength which says how competetive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them grater */
    public int $multiplier = 3;

    public function getRaceResults()
    {
        $drivers = (new DriversRepository)->findAllWithTeams();
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

            $results[] = $driver;
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

        /* Get drivers from given team */
        foreach ($drivers as $key => $driver) {
            if($driver['team_name'] == $teamName) $teamDrivers[] = $driver;
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
            if($driver['team_name'] == $teamName) $driversWhoFinished++;
        }

        if ($driversWhoFinished == 2) return true;

        return false;
    }

    public function getTeamsStrength()
    {
        return [
            'Mercedes' => 17,
            'Ferrari' => 13.7,
            'Red Bull' => 13.6,
            'Mclaren' => 4.4,
            'Renualt' => 4.2,
            'Racing Point' => 4.1,
            'Toro Rosso' => 3.9,
            'Haas' => 3.7,
            'Alfa Romeo' => 3.7,
            'Williams' => 0.6
        ];
    }
}