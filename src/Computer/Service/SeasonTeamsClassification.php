<?php

declare(strict_types=1);

namespace Computer\Service;

use Computer\Model\TeamsClassification;
use Computer\Model\TeamSeasonResult;
use Computer\Repository\SeasonRepository;
use Computer\Service\TeamStatistics\TeamPoints;
use Domain\DomainFacadeInterface;

class SeasonTeamsClassification
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    public function getClassification(int $userId): TeamsClassification
    {
        $teams = $this->domainFacade->getAllTeams();
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
        $teams = $this->domainFacade->getAllTeams();

        return TeamsClassification::createDefaultClassification($teams);
    }
}
