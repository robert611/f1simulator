<?php 

namespace App\Model\Classification;

use App\Model\DriverStatistics\LeaguePlayerPoints;

class LeagueClassifications 
{
    public object $league;
    public object $leaguePlayerPoints;
    public ?int $raceId;

    public function __construct(object $league, ?int $raceId)
    {
        $this->league = $league;
        $this->raceId = $raceId;
        $this->leaguePlayerPoints = new LeaguePlayerPoints();
    }

    public function getClassificationBasedOnType(string $type)
    {
        $classification = null;

        switch ($type) {
            case 'race':
                $classification = $this->getRaceClassification();
                break;  
            case 'drivers':
                $classification = $this->getPlayersClassification();
                break;
            case 'qualifications':
                $classification = $this->getQualificationsClassification();
                break;
            default: 
                $classification = $this->getQualificationsClassification(); /* It matches the default option in html */
        }

        return $classification;
    }

    private function getRaceClassification(): object
    {
        $leaguePlayerPoints = $this->leaguePlayerPoints;

        $race = $this->findRace($this->raceId);

        /* Set points to raceResults */
        $race->getRaceResults()->map(function($result) use ($leaguePlayerPoints) {
            $points = $leaguePlayerPoints->getPlayerPointsByRace($result);
            $result->setPoints($points);
        });

        return $race->getRaceResults();
    }

    private function getPlayersClassification(): array
    {
        $players = $this->league->getPlayers();

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        foreach ($players as $player) {
            $points = $this->leaguePlayerPoints->getPlayerPoints($player);
            $player->setPoints($points);
        }

        return $this->setPlayersPositions([...$players]);
    }

    private function getQualificationsClassification()
    {
        $race = $this->findRace($this->raceId);

        return $race->getQualifications();
    }

    private function findRace(int $id): object
    {
        $raceId = $this->raceId;

        $race = $this->league->getRaces()->filter(function($race) use ($raceId) {
            return $race->getId() == $raceId;
        })->first();

        return $race;
    }

    private function setPlayersPositions($drivers): array
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