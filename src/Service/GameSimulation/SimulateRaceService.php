<?php

declare(strict_types=1);

namespace App\Service\GameSimulation;

use App\Model\Configuration\QualificationAdvantage;
use App\Model\Configuration\TeamsStrength;
use App\Model\GameSimulation\QualificationResultsCollection;
use Computer\Entity\Qualification;
use Computer\Entity\Race;
use Computer\Entity\RaceResult;
use Computer\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;
use Domain\Entity\Driver;
use Domain\Repository\DriverRepository;
use Domain\Repository\TrackRepository;
use Multiplayer\Model\GameSimulation\LeagueQualificationResultsCollection;

class SimulateRaceService
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies the strength of the teams
     by some value to make differences between them grater */
    public int $multiplier = 3;

    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly SimulateQualifications $simulateQualifications,
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

        $coupons = $this->generateCoupons($qualificationResults->toPlainArray());

        for ($position = 1; $position <= count($drivers); $position++) {
            do {
                $driverId = $coupons[rand(0, count($coupons) - 1)];
            } while (in_array($driverId, $results));

            $results[$position] = $driverId;
        }

        return $results;
    }

    /**
     * @param Driver[] $drivers
     *
     * @return int[]
     */
    public function getLeagueRaceResults(
        array $drivers,
        LeagueQualificationResultsCollection $qualificationsResults,
    ): array {
        $results = [];

        $coupons = $this->generateCoupons($qualificationsResults->toPlainDriverArray());

        for ($position = 1; $position <= count($drivers); $position++) {
            do {
                $driverId = $coupons[array_rand($coupons)];
            } while (in_array($driverId, $results));

            $results[$position] = $driverId;
        }

        return $results;
    }

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
