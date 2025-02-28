<?php 

namespace App\Service\GameSimulation;

use App\Entity\Qualification;
use App\Entity\Race;
use App\Entity\RaceResult;
use App\Entity\Season;
use App\Model\Configuration\QualificationAdvantage;
use App\Model\Configuration\TeamsStrength;
use App\Model\GameSimulation\QualificationResultsCollection;
use App\Repository\DriverRepository;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;

class SimulateRaceService
{
    /* Every team has it's strength which says how competitive team is, multiplier multiplies strength of the teams by some value to make diffrences beetwen them grater */
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
            /** @TODO poleganie na statycznych id rekordów w tabeli trackRepository nie ma sensu */
            $track = $this->trackRepository->find($lastRace->getTrack()->getId() + 1);
        } else {
            $track = $this->trackRepository->findOneBy([]);
        }

        $race = Race::create($track, $season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        $qualificationResultsCollection = $this->simulateQualifications->getQualificationsResults();

        $raceResults = $this->getRaceResults($qualificationResultsCollection);

        /* Save qualifications results in database */
        foreach ($qualificationResultsCollection->getQualificationResults() as $qualificationResult) {
            $qualification = new Qualification();
            $qualification->setRace($race);
            $qualification->setDriver($qualificationResult->getDriver());
            $qualification->setPosition($qualificationResult->getPosition());

            $this->entityManager->persist($qualification);
        }

        /* Save race results in database */
        foreach ($raceResults as $position => $driverId) {
            $raceResult = new RaceResult();

            $raceResult->setRace($race);
            $raceResult->setDriver($this->driverRepository->find($driverId));
            $raceResult->setPosition($position);

            $this->entityManager->persist($raceResult);
        }

        $this->entityManager->flush();
    }

    public function getRaceResults(QualificationResultsCollection $qualificationResults): array
    {
        $drivers = $this->driverRepository->findAll();

        $results = [];

        $coupons = $this->getCoupons($qualificationResults->toPlainArray());

        for ($position = 1; $position <= count($drivers); $position++) {
            do {
                $driverId = $coupons[rand(0, count($coupons) - 1)];
            } while (in_array($driverId, $results));

            $results[$position] = $driverId;
        }

        return $results;
    }

    public function getLeagueRaceResults($drivers, $qualificationsResults): array
    {
        $results = [];

        $coupons = $this->getCoupons($qualificationsResults);

        for ($i = 1; $i <= count($drivers); $i++) {
            do {
                $driverId = $coupons[rand(0, count($coupons) - 1)];
            } while(in_array($driverId, $results));

            $results[$i] = $driverId;
        }

        return $results;
    }

    public function getCoupons(array $qualificationsResults): array
    {
        $teams = TeamsStrength::getTeamsStrength();
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