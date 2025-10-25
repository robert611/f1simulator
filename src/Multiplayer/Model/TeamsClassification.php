<?php

declare(strict_types=1);

namespace Multiplayer\Model;

use Domain\Entity\Team;

class TeamsClassification
{
    /** @var TeamLeagueResult[] $teamsSeasonResults */
    private array $teamsSeasonResults;

    /**
     * @return TeamLeagueResult[]
     */
    public function getTeamsSeasonResults(): array
    {
        return $this->teamsSeasonResults;
    }

    /**
     * This classification is shown if there is no active user season
     * All teams have zero points and are displayed in random order
     *
     * @param Team[] $teams
     */
    public static function createDefaultClassification(array $teams): self
    {
        $teamsSeasonResults = [];

        $position = 1;

        foreach ($teams as $team) {
            $teamsSeasonResults[] = TeamLeagueResult::create($team, 0, $position);
            $position += 1;
        }

        $teamsClassification = new self();
        $teamsClassification->teamsSeasonResults = $teamsSeasonResults;

        return $teamsClassification;
    }

    /**
     * @param TeamLeagueResult[] $teamsSeasonResults
     */
    public static function create(array $teamsSeasonResults): self
    {
        usort($teamsSeasonResults, function (TeamLeagueResult $a, TeamLeagueResult $b): int {
            return $a->getPosition() <=> $b->getPosition();
        });

        $teamsClassification = new self();
        $teamsClassification->teamsSeasonResults = $teamsSeasonResults;

        return $teamsClassification;
    }
}
