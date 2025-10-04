<?php

namespace App\Service\GameSimulation;

use App\Entity\Driver;
use App\Model\GameSimulation\QualificationResult;
use App\Model\GameSimulation\QualificationResultsCollection;
use App\Repository\DriverRepository;

class SimulateQualifications
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly QualificationsHelperService $helperService,
    ) {
    }

    public function getQualificationsResults(): QualificationResultsCollection
    {
        $drivers = $this->driverRepository->findAll();

        $result = QualificationResultsCollection::create();

        $coupons = $this->helperService->generateCoupons();

        for ($position = 1; $position <= count($drivers); $position++) {
            /* If both driver from given team will be already drawn, check function will return true and draw will be repeat until $team with only one or zero drivers finished will be drawn */
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $result->toPlainArray()));

            /* At this point team from which driver will be draw is drawn, not the driver per se so now draw one of the drivers from that team and put him in finished drivers */
            $driver = $this->drawDriverFromATeam($teamName, $drivers, $result->toPlainArray());

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
     * @param Driver[] $drivers
     * @param Driver[] $results
     */
    public function drawDriverFromATeam(string $teamName, array $drivers, array $results): ?Driver
    {
        $teamDrivers = array();

        shuffle($drivers);

        /* Get drivers from given team */
        foreach ($drivers as $driver) {
            if ($driver->getTeam()->getName() == $teamName) {
                $teamDrivers[] = $driver;
            }
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
        } elseif (in_array($teamDrivers[1], $results)) {
            return $teamDrivers[0];
        }

        /* If none of the drivers finished then draw one of them */
        return $teamDrivers[rand(0, 1)];
    }
}
