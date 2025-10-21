<?php

declare(strict_types=1);

namespace Computer\Service;

use App\Model\TeamsClassification;
use App\Model\TeamSeasonResult;
use Computer\Repository\SeasonRepository;
use Computer\Service\TeamStatistics\TeamPoints;
use Domain\Repository\TeamRepository;

class SeasonTeamsClassification
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly SeasonRepository $seasonRepository,
    ) {
    }

    public function getClassification(int $userId): TeamsClassification
    {
        $teams = $this->teamRepository->findAll();
        $season = $this->seasonRepository->findOneBy(['user' => $userId, 'completed' => 0]);

        $teamsPointsTable = [];

        foreach ($teams as $team) {
            if ($season) {
                $teamsPointsTable[$team->getId()] = TeamPoints::getTeamPoints($team, $season);
                continue;
            }

            $teamsPointsTable[$team->getId()] = 0;
        }

        // Sorts using descending order and preserves array keys
        arsort($teamsPointsTable);

        $teamSeasonResults = [];

        foreach ($teams as $team) {
            $keyPosition = array_search($team->getId(), array_keys($teamsPointsTable));
            $position = $keyPosition + 1;
            $teamSeasonResults[] = TeamSeasonResult::create($team, $teamsPointsTable[$team->getId()], $position);
        }

        return TeamsClassification::create($teamSeasonResults);
    }

    public function getDefaultTeamsClassification(): TeamsClassification
    {
        $teams = $this->teamRepository->findAll();

        return TeamsClassification::createDefaultClassification($teams);
    }
}
