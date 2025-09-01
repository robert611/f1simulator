<?php 

declare(strict_types=1);

namespace App\Service\Classification;

use App\Entity\UserSeason;
use App\Entity\UserSeasonRace;
use Doctrine\Common\Collections\Collection;

class LeagueClassifications 
{
    public function getClassificationBasedOnType(UserSeason $league, ?int $raceId, ClassificationType $type)
    {
        // @TODO, this return type must be unified, maybe some dto with empty values in case of problems?
        return match ($type) {
            ClassificationType::RACE => $this->getRaceClassification($league, $raceId),
            ClassificationType::PLAYERS => $this->getPlayersClassification($league),
            default => $this->getQualificationsClassification($league, $raceId), /* It matches the default option in HTML */
        };
    }

    private function getRaceClassification(UserSeason $league, ?int $raceId): object
    {
        // @TODO, race could be null, but it's not really handled, does this make sense?
        $race = $this->findRace($league, $raceId);

        return $race->getRaceResults();
    }

    private function getPlayersClassification(UserSeason $league): array
    {
        $players = $league->getPlayers()->toArray();

        // @TODO, currently players have no points assigned, sorting will not work

        /* Sort drivers according to possessed points */
        usort($players, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });

        return $players;
    }

    private function getQualificationsClassification(UserSeason $league, ?int $raceId): ?Collection
    {
        $race = $this->findRace($league, $raceId);

        return $race?->getQualifications();
    }

    private function findRace(UserSeason $league, ?int $id): ?UserSeasonRace
    {
        // @TODO is this checking really required, can't I just get the race from database by id?
        $race = $league->getRaces()->filter(function($race) use ($id) {
            return $race->getId() === $id;
        })->first();

        /* Just in case if this classification will be called without giving id, return some results */
        /* HTML will show proper race label */
        if (false === $race) {
            $race = $league->getRaces()->first();
        }

        if (false === $race) {
            return null;
        }

        return $race;
    }
}