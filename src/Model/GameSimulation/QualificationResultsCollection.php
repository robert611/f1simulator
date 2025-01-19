<?php

declare(strict_types=1);

namespace App\Model\GameSimulation;

use App\Entity\Driver;

class QualificationResultsCollection
{
    /** @var QualificationResult[] */
    private array $qualificationResults;

    public function getQualificationResults(): array
    {
        return $this->qualificationResults;
    }

    /**
     * @return array{int, Driver}
     */
    public function toPlainArray(): array
    {
        $plainArray = [];

        foreach ($this->qualificationResults as $qualificationResult) {
            $plainArray[$qualificationResult->getPosition()] = $qualificationResult->getDriver();
        }

        return $plainArray;
    }

    public function addQualificationResult(QualificationResult $qualificationResult): void
    {
        $this->qualificationResults[] = $qualificationResult;
    }

    /**
     * @param QualificationResult[] $qualificationResults
     */
    public static function create(array $qualificationResults = []): self
    {
        $qualificationResultsCollection = new self();
        $qualificationResultsCollection->qualificationResults = $qualificationResults;

        return $qualificationResultsCollection;
    }
}
