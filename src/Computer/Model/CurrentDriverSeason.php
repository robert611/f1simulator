<?php

declare(strict_types=1);

namespace Computer\Model;

use Computer\Entity\Race;
use Computer\Entity\Season;
use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TrackDTO;
use Domain\Contract\Model\DriverPodiumsDTO;

class CurrentDriverSeason
{
    private Season $season;
    private DriverDTO $driver;
    private int $driverPoints;
    private DriverPodiumsDTO $driverPodiums;
    private ?TrackDTO $currentTrack;
    private int $numberOfRaces;
    private mixed $classification; // Nie ma wspólnego typu danych, bo klasyfikacji mogą być trzy typy
    private TeamsClassification $teamsClassification;
    private ?Race $classificationRace;

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function getDriver(): DriverDTO
    {
        return $this->driver;
    }

    public function getDriverPoints(): int
    {
        return $this->driverPoints;
    }

    public function getDriverPodiums(): DriverPodiumsDTO
    {
        return $this->driverPodiums;
    }

    public function getCurrentTrack(): ?TrackDTO
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
        DriverDTO $driver,
        int $driverPoints,
        DriverPodiumsDTO $driverPodiums,
        ?TrackDTO $currentTrack,
        int $numberOfRaces,
        mixed $classification,
        TeamsClassification $teamsClassification,
        ?Race $classificationRace,
    ): self {
        $currentDriverSeason = new self();
        $currentDriverSeason->season = $season;
        $currentDriverSeason->driver = $driver;
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
