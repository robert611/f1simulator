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
            case 'players':
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
        $race = $this->findRace($this->raceId);

        /* Set points to raceResults */
        $race->getRaceResults()->map(function($result) {
            $points = $this->leaguePlayerPoints->getPlayerPointsByResult($result);
            $result->setPoints($points);
        });

        return $race->getRaceResults();
    }

    private function getPlayersClassification(): array
    {
        $players = $this->league->getPlayers();

        /* In default drivers have no assign points got in current season in database, so it has to be done here */
        $players->map(function($player) {
            $points = $this->leaguePlayerPoints->getPlayerPoints($player);
            $player->setPoints($points);
        });

        $players = [...$players];

        /* Sort drivers according to possesd points */
        usort ($players, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        return $players;
    }

    private function getQualificationsClassification()
    {
        $race = $this->findRace($this->raceId);

        return $race ? $race->getQualifications() : null;
    }

    private function findRace(?int $id): ?object
    {
        $race = $this->league->getRaces()->filter(function($race) use ($id) {
            return $race->getId() == $id;
        })->first();

        /* Just in case if this classification will be called without giving id, return some results */
        /* HTML will show proper race label */
        $race ? $race : $race = $this->league->getRaces()->first();

        if (is_bool($race)) {
            return null;
        }

        return $race;
    }
}