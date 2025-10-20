<?php

declare(strict_types=1);

namespace App\Model\GameSimulation;

use Domain\Entity\Driver;
use Multiplayer\Entity\UserSeasonPlayer;

class LeagueQualificationResultsCollection
{
    /** @var LeagueQualificationResult[] */
    private array $leagueQualificationResults;

    /**
     * @return LeagueQualificationResult[]
     */
    public function getQualificationResults(): array
    {
        return $this->leagueQualificationResults;
    }

    public function addQualificationResult(LeagueQualificationResult $qualificationResult): void
    {
        $this->leagueQualificationResults[] = $qualificationResult;
    }

    /**
     * @return array<int, UserSeasonPlayer>
     */
    public function toPlainArray(): array
    {
        $plainArray = [];

        foreach ($this->leagueQualificationResults as $qualificationResult) {
            $plainArray[$qualificationResult->getPosition()] = $qualificationResult->getUserSeasonPlayer();
        }

        return $plainArray;
    }

    /**
     * @return array<int, Driver>
     */
    public function toPlainDriverArray(): array
    {
        $plainArray = [];

        foreach ($this->leagueQualificationResults as $qualificationResult) {
            $plainArray[$qualificationResult->getPosition()] = $qualificationResult->getUserSeasonPlayer()->getDriver();
        }

        return $plainArray;
    }

    public static function create(array $qualificationResults = []): self
    {
        $leagueQualificationResultsCollection = new self();
        $leagueQualificationResultsCollection->leagueQualificationResults = $qualificationResults;

        return $leagueQualificationResultsCollection;
    }
}
