<?php

declare(strict_types=1);

namespace App\Service\DriverStatistics;

use App\Entity\Driver;
use App\Entity\Season;
use App\Model\DriverPodiumsDTO;

class DriverPodiumsService
{
    public static function getDriverPodiums(Driver $driver, Season $season): array
    {
        $races = $season->getRaces();

        $podiumsTable = self::getPodiumsTable();

        foreach ($races as $race) {
            $raceResultCollection = $driver->getRaceResults()->filter(function ($result) use ($race) {
                return $result->getRace()->getId() === $race->getId();
            });

            if ($raceResultCollection->isEmpty()) {
                continue;
            }

            $position = $raceResultCollection->first()->getPosition();

            if ($position >= 1 && $position <= 3) {
                $podiumsTable[$position] += 1;
            }
        }

        return $podiumsTable;
    }

    public static function getDriverPodiumsDTO(Driver $driver, Season $season): DriverPodiumsDTO
    {
        $podiumsTable = self::getDriverPodiums($driver, $season);

        return DriverPodiumsDTO::create(
            $podiumsTable[1],
            $podiumsTable[2],
            $podiumsTable[3],
        );
    }

    public static function getPodiumsTable(): array
    {
        return [
            1 => 0,
            2 => 0,
            3 => 0
        ];
    }
}
