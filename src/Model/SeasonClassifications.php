<?php 

namespace App\Model;

use App\Drivers;

class SeasonClassifications 
{
    public array $drivers;
    public $season;
    public object $qualificationRepository;
    public object $raceResultsRepository;

    public function __construct(array $drivers, $season, object $qualificationRepository, object $raceResultsRepository)
    {
        $this->drivers = $drivers;
        $this->season = $season;
        $this->qualificationRepository = $qualificationRepository;
        $this->raceResultsRepository = $raceResultsRepository;
    }

    public function getClassificationBasedOnType(string $type)
    {
        $classification = null;

        if (!$this->season) {
            return $this->getDriversClassification();
        }

        switch ($type) {
            case 'race':
                $classification = $this->getLastRaceResults();
                break;  
            case 'drivers':
                $classification = $this->getDriversClassification();
                break;
            case 'qualifications':
                $classification = $this->getLastQualificationsResults();
                break;
        }

        return $classification;
    }

    public function getDriversClassification()
    {
        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            if ($this->season) {
                $points = (new DriverPoints($this->raceResultsRepository))->getDriverPoints($driver->getId(), $this->season);
                $driver->setPoints($points);
                
                if ($driver->getCarId() == $this->season->getCarId()) {
                    $driver->isUser = true;
                    $driver->setName($this->season->getUser()->getUsername());
                    $driver->setSurname('');
                }
            } else {
                $driver->setPoints(0);
            }
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getLastRaceResults(): array
    {
        $lastRace = $this->season->getRaces()[count($this->season->getRaces()) - 1];

        $driverPoints = new DriverPoints($this->raceResultsRepository);

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {

            if ($this->season) {
                $points = $driverPoints->getDriverPointsByRace($driver->getId(), $lastRace);

                $driver->setPoints($points);
            } else {
                $driver->setPoints(0);
            }

            
            if ($driver->getCarId() == $this->season->getCarId()) {
                $driver->isUser = true;
                $driver->setName($this->season->getUser()->getUsername());
                $driver->setSurname('');
            }
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getLastQualificationsResults()
    {
        $lastRace = $this->season->getRaces()[count($this->season->getRaces()) - 1];

        $lastQualification = $this->qualificationRepository->findBy(['race' => $lastRace->getId()]);

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($lastQualification as &$result) {
            if ($result->getDriver()->getCarId() == $this->season->getCarId()) {
                $result->getDriver()->setName($this->season->getUser()->getUsername());
                $result->getDriver()->setSurname('');
                $result->getDriver()->isUser = true;
            }
        }

        return $lastQualification;
    }

    public function setDriversPositions($drivers)
    {
        /* Sort drivers according to got points */
        usort ($drivers, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => &$driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }
}