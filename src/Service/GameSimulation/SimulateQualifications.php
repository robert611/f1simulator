<?php 

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Model\Configuration\TeamsStrength;
use App\Model\GameSimulation\QualificationResult;
use App\Model\GameSimulation\QualificationResultsCollection;
use App\Repository\DriverRepository;

class SimulateQualifications
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them greater */
    public int $multiplier = 3;

    public function __construct(
        private readonly DriverRepository $driverRepository,
    ) {
    }

    /**
     * @param Driver[] $drivers
     *
     * @return Driver[]
     */
    public function getLeagueQualificationsResults(array $drivers): array
    {
        $results = [];

        $coupons = $this->getCoupons();

        for ($i = 1, $j = count($drivers); $i <= $j; $i++) {
            /* If both drivers from given team are already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do {
                $teamName = $coupons[rand(1, count($coupons))];
            } while($this->checkIfBothDriversFromTeamAlreadyFinished($teamName, $results));

            /* At this point team from which a driver will be draw is drawn, not the driver per se so now draw one of the drivers from that team and put him in finished drivers */
            $driver = $this->drawDriverFromTeam($teamName, $drivers, $results);

            /* If there is no drawn driver, then iterate once again */
            $driver ? $results[$i] = $driver : $i--;
        }

        return $results;
    }

    public function getQualificationsResults(): QualificationResultsCollection
    {
        $drivers = $this->driverRepository->findAll();

        $result = QualificationResultsCollection::create();

        $coupons = $this->getCoupons();

        for ($position = 1; $position <= count($drivers); $position++) {
            /* If both driver from given team will be already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do {
                $teamName = $coupons[rand(1, count($coupons))];
            } while ($this->checkIfBothDriversFromTeamAlreadyFinished($teamName, $result->toPlainArray()));

            /* At this point team from which driver will be draw is drawn, not the driver per se so now draw one of the drivers from that team and put him in finished drivers */
            $driver = $this->drawDriverFromTeam($teamName, $drivers, $result->toPlainArray());

            if ($driver) {
                $qualificationResult = QualificationResult::create($driver, $position);
                $result->addQualificationResult($qualificationResult);
                continue;
            }

            /* If there is no drawn driver, then iterate once again */
            $position -= 1;
        }

        return $result;
    }

    /**
     * @return array{int, string}
     *
     * For instance [1 => "Mercedes", 2 => "Mercedes", 3 => "Red Bull"]
     */
    public function getCoupons(): array
    {
        $teams = TeamsStrength::getTeamsStrength();
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

        /* In this case it's called by league qualifications, and there may not be two drivers in a team */
        if (count($teamDrivers) < 2) {
            if (isset($teamDrivers[0]) && !in_array($teamDrivers[0], $results)) {
                return $teamDrivers[0];
            }

            return null;
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
            if ($driver->getTeam()->getName() == $teamName) {
                $driversWhoFinished++;
            }
        }

        if ($driversWhoFinished == 2) {
            return true;
        }

        return false;
    }
}