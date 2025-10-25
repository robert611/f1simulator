<?php

declare(strict_types=1);

namespace Computer\Model\GameSimulation;

use Domain\Entity\Driver;

class QualificationResultsCollection
{
    /** @var QualificationResult[] */
    private array $qualificationResults;

    /**
     * @return QualificationResult[]
     */
    public function getQualificationResults(): array
    {
        return $this->qualificationResults;
    }

    public function addQualificationResult(QualificationResult $qualificationResult): void
    {
        $this->qualificationResults[] = $qualificationResult;
    }

    /**
     * @return array<int, Driver>
     */
    public function toPlainArray(): array
    {
        $plainArray = [];

        foreach ($this->qualificationResults as $qualificationResult) {
            $plainArray[$qualificationResult->getPosition()] = $qualificationResult->getDriver();
        }

        return $plainArray;
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
