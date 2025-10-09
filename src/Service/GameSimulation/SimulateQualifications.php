<?php

declare(strict_types=1);

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
            // Draw a team that has unfinished driver
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while ($this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $result->toPlainArray()));

            // Draw a driver from the selected team
            $driver = $this->drawDriverFromATeam($teamName, $drivers, $result->toPlainArray());

            if ($driver) {
                $qualificationResult = QualificationResult::create($driver, $position);
                $result->addQualificationResult($qualificationResult);
                continue;
            }

            // Retry this position if no driver was drawn
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
        $teamDrivers = [];

        $normalizedTeamName = strtolower($teamName);

        /* Get drivers from a given team */
        foreach ($drivers as $driver) {
            if (strtolower($driver->getTeam()->getName()) === $normalizedTeamName) {
                $teamDrivers[] = $driver;
            }
        }

        $finishedDriverIds = [];
        foreach ($results as $finishedDriver) {
            $finishedDriverIds[$finishedDriver->getId()] = true;
        }

        $unfinishedDrivers = array_values(array_filter(
            $teamDrivers,
            static function (Driver $driver) use ($finishedDriverIds): bool {
                return !isset($finishedDriverIds[$driver->getId()]);
            },
        ));

        if (count($unfinishedDrivers) === 0) {
            return null;
        }

        return $unfinishedDrivers[array_rand($unfinishedDrivers)];
    }
}
