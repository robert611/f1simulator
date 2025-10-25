<?php

declare(strict_types=1);

namespace Domain\Service\GameSimulation;

use App\Model\GameSimulation\QualificationResultsCollection;
use Computer\Entity\Qualification;
use Computer\Entity\Race;
use Computer\Entity\RaceResult;
use Computer\Entity\Season;
use Computer\Service\GameSimulation\SimulateQualifications;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Repository\DriverRepository;
use Domain\Repository\TrackRepository;

class SimulateRaceService
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly SimulateQualifications $simulateQualifications,
        private readonly CouponsGenerator $couponsGenerator,
        private readonly TrackRepository $trackRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function simulateRace(Season $season): void
    {
        $lastRace = $season->getRaces()->last();

        if ($lastRace) {
            $track = $this->trackRepository->getNextTrack($lastRace->getTrack()->getId());
        } else {
            $track = $this->trackRepository->getFirstTrack();
        }

        $race = Race::create($track, $season);
        $season->addRace($race);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        $qualificationResultsCollection = $this->simulateQualifications->getQualificationsResults();

        $raceResults = $this->getRaceResults($qualificationResultsCollection);

        /* Save qualification results in a database */
        foreach ($qualificationResultsCollection->getQualificationResults() as $qualificationResult) {
            $qualification = Qualification::create(
                $qualificationResult->getDriver(),
                $race,
                $qualificationResult->getPosition(),
            );
            $race->addQualification($qualification);

            $this->entityManager->persist($qualification);
        }

        /* Save race results in a database */
        foreach ($raceResults as $position => $driverId) {
            $driver = $this->driverRepository->find($driverId);
            $raceResult = RaceResult::create($position, $race, $driver);
            $race->addRaceResult($raceResult);

            $this->entityManager->persist($raceResult);
        }

        $this->entityManager->flush();
    }

    public function getRaceResults(QualificationResultsCollection $qualificationResults): array
    {
        $drivers = $this->driverRepository->findAll();

        $results = [];

        $coupons = $this->couponsGenerator->generateCoupons($qualificationResults->toPlainArray());

        for ($position = 1; $position <= count($drivers); $position++) {
            do {
                $driverId = $coupons[rand(0, count($coupons) - 1)];
            } while (in_array($driverId, $results));

            $results[$position] = $driverId;
        }

        return $results;
    }
}
