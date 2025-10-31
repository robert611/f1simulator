<?php

declare(strict_types=1);

namespace Computer\Service\GameSimulation;

use Computer\Model\GameSimulation\QualificationResult;
use Computer\Model\GameSimulation\QualificationResultsCollection;
use Domain\DomainFacadeInterface;
use Domain\Service\GameSimulation\QualificationsHelperService;

class SimulateQualifications
{
    public function __construct(
        private readonly QualificationsHelperService $helperService,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function getQualificationsResults(): QualificationResultsCollection
    {
        $drivers = $this->domainFacade->getAllDrivers();

        $result = QualificationResultsCollection::create();

        $coupons = $this->helperService->generateCoupons();

        for ($position = 1; $position <= count($drivers); $position++) {
            // Draw a team that has unfinished driver
            do {
                $teamName = $coupons[array_rand($coupons)];
            } while (
                $this->helperService->checkIfBothDriversFromATeamAlreadyFinished($teamName, $result->toPlainArray())
            );

            // Draw a driver from the selected team
            $driver = $this->helperService->drawDriverFromATeam($teamName, $drivers, $result->toPlainArray());

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
}
