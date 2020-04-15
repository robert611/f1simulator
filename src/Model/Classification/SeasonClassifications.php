<?php 

namespace App\Model\Classification;

use App\Model\DriverStatistics\DriverPoints;
use App\Entity\Season;
use App\Entity\UserSeason;

class SeasonClassifications 
{
    public $drivers;
    public object $driverPoints;
    public $season;

    public function __construct($drivers, $season)
    {
        $this->drivers = $drivers;
        $this->driverPoints = new DriverPoints();
        $this->season = $season;
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
            default: 
                $classification = $this->getLastQualificationsResults(); /* It matches the default option in html */
        }

        $classification = $this->setUserToDriver($classification);

        return $classification;
    }

    public function getDriversClassification()
    {
        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            $points = $this->driverPoints->getDriverPoints($driver, $this->season);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getLastRaceResults(): array
    {
        $lastRace = $this->season->getRaces()->last();

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            if ($this->season) {
                $points = $this->driverPoints->getDriverPointsByRace($driver, $lastRace);

                $driver->setPoints($points);
            } else {
                $driver->setPoints(0);
            }
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getLastQualificationsResults(): array
    {
        $results = array();

        $lastRace = $this->season->getRaces()->last();

        $lastQualification = $lastRace->getQualifications();

        foreach ($lastQualification as $result) {
            $result->getDriver()->setPosition($result->getPosition());
            $results[] = $result->getDriver();
        }

        return $results;
    }

    public function setUserToDriver(array $results): array
    {
        foreach ($results as $driver) {
            if ($driver->getCarId() == $this->season->getCarId()) {
                $driver->setName($this->season->getUser()->getUsername());
                $driver->setSurname('');
                $driver->isUser = true;
            }
        }

        return $results;
    }

    public function setDriversPositions($drivers)
    {
        /* Sort drivers according to possesd points */
        usort ($drivers, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        foreach ($drivers as $key => &$driver) {
            $driver->setPosition($key + 1);
        }

        return $drivers;
    }
}