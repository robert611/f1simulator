<?php 

namespace App\Model\Classification;

use App\Model\DriverStatistics\DriverPoints;
use App\Entity\Season;
use App\Entity\UserSeason;

class SeasonClassifications 
{
    public $drivers;
    public object $driverPoints;
    public ?object $season;
    public $raceId;

    public function __construct($drivers, ?object $season, $raceId)
    {
        $this->drivers = $drivers;
        $this->driverPoints = new DriverPoints();
        $this->season = $season;
        $this->raceId = $raceId;
    }

    public function getClassificationBasedOnType(string $type)
    {
        $classification = null;

        if (!$this->season) {
            return $this->getDriversClassification();
        }

        switch ($type) {
            case 'race':
                $classification = $this->getRaceClassification();
                break;  
            case 'drivers':
                $classification = $this->getDriversClassification();
                break;
            case 'qualifications':
                $classification = $this->getQualificationsClassification();
                break;
            default: 
                $classification = $this->getQualificationsClassification(); /* It matches the default option in html */
        }

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

    public function getRaceClassification(): array
    {
        $race = $this->findRace($this->raceId);

        /* In default drivers have no assign points in a database, so it has to be done here */
        foreach ($this->drivers as &$driver) {
            $points = $this->driverPoints->getDriverPointsByRace($driver, $race);
            $driver->setPoints($points);
        }

        return $this->setDriversPositions($this->drivers);
    }

    public function getQualificationsClassification(): object
    {
        $race = $this->findRace($this->raceId);

        return $race->getQualifications();
    }

    private function findRace($id): object
    {
        $race = $this->season->getRaces()->filter(function($race) use ($id) {
            return $race->getId() == $id;
        })->first();

        /* If user typed unproper race id, it matches the default name in twig */
        if (!$race) {
            $race = $this->season->getRaces()->first();
        }

        return $race;
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