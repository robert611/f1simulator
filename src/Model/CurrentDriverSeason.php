<?php

declare(strict_types=1);

namespace App\Model;

use Computer\Entity\Race;
use Computer\Entity\Season;
use Domain\Entity\Track;

class CurrentDriverSeason
{
    private Season $season;
    private int $driverPoints;
    private DriverPodiumsDTO $driverPodiums;
    private ?Track $currentTrack;
    private int $numberOfRaces;
    private mixed $classification; // Nie ma wspólnego typu danych, bo klasyfikacji mogą być trzy typy
    private TeamsClassification $teamsClassification;
    private ?Race $classificationRace;

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function getDriverPoints(): int
    {
        return $this->driverPoints;
    }

    public function getDriverPodiums(): DriverPodiumsDTO
    {
        return $this->driverPodiums;
    }

    public function getCurrentTrack(): ?Track
    {
        return $this->currentTrack;
    }

    public function getNumberOfRaces(): int
    {
        return $this->numberOfRaces;
    }

    public function getClassification(): mixed
    {
        return $this->classification;
    }

    public function getTeamsClassification(): ?TeamsClassification
    {
        return $this->teamsClassification;
    }

    public function getClassificationRace(): ?Race
    {
        return $this->classificationRace;
    }

    public static function create(
        Season $season,
        int $driverPoints,
        DriverPodiumsDTO $driverPodiums,
        ?Track $currentTrack,
        int $numberOfRaces,
        mixed $classification,
        TeamsClassification $teamsClassification,
        ?Race $classificationRace,
    ): self {
        $currentDriverSeason = new self();
        $currentDriverSeason->season = $season;
        $currentDriverSeason->driverPoints = $driverPoints;
        $currentDriverSeason->driverPodiums = $driverPodiums;
        $currentDriverSeason->currentTrack = $currentTrack;
        $currentDriverSeason->numberOfRaces = $numberOfRaces;
        $currentDriverSeason->classification = $classification;
        $currentDriverSeason->teamsClassification = $teamsClassification;
        $currentDriverSeason->classificationRace = $classificationRace;

        return $currentDriverSeason;
    }
}
