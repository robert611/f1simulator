<?php

declare(strict_types=1);

namespace App\Model\GameSimulation;

class LeagueRaceResultsDTO
{
    private LeagueQualificationResultsCollection $qualificationsResults;

    private array $raceResults;

    public function getQualificationsResults(): LeagueQualificationResultsCollection
    {
        return $this->qualificationsResults;
    }

    public function getRaceResults(): array
    {
        return $this->raceResults;
    }

    public static function create(LeagueQualificationResultsCollection $qualificationsResults, array $raceResults): self
    {
        $leagueRaceResultsDTO = new self();
        $leagueRaceResultsDTO->qualificationsResults = $qualificationsResults;
        $leagueRaceResultsDTO->raceResults = $raceResults;

        return $leagueRaceResultsDTO;
    }
}
