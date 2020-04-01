<?php 

namespace App\Model;

use App\Model\DriverPoints;

class SeasonClassifications 
{
    public array $drivers;
    public object $driverPoints;
    public $season;

    public function __construct(array $drivers, $season)
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

        return $classification;
    }

    public function getDriversClassification()
    {
        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            if ($this->season) {
                $points = $this->driverPoints->getDriverPoints($driver, $this->season);
                $driver->setPoints($points);
                
                $driver = $this->setUserToDriver($driver, $this->season);
            } else {
                $driver->setPoints(0);
            }
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

            $driver = $this->setUserToDriver($driver, $this->season);
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getLastQualificationsResults()
    {
        $lastRace = $this->season->getRaces()->last();

        $lastQualification = $lastRace->getQualifications();

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($lastQualification as $result) {
            $result->setDriver($this->setUserToDriver($result->getDriver(), $this->season));
        }

        return $lastQualification;
    }

    public function setUserToDriver(object $driver, object $season)
    {
        if ($driver->getCarId() == $season->getCarId()) {
            $driver->setName($season->getUser()->getUsername());
            $driver->setSurname('');
            $driver->isUser = true;
        }

        return $driver;
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