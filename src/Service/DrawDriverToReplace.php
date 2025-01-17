<?php 

declare(strict_types=1);

namespace App\Service;

use App\Entity\Driver;
use App\Entity\Team;

class DrawDriverToReplace
{
    /**
     * Returns of on given team drivers in a random order
     */
    public function getDriverToReplace(Team $team): Driver
    {
        /** @var Driver[] $drivers */
        $drivers = $team->getDrivers()->toArray();

        // Reindex array to make sure it starts from 0
        $drivers = array_values($drivers);

        $driversLength = count($drivers) - 1;

        $random = rand(0, $driversLength);

        return $drivers[$random];
    }

    public function getDriverToReplaceInUserLeague(array $drivers, ?object $leaguePlayers): object
    {
        $takenDrivers = $leaguePlayers ? $this->getTakenDriversFromLeague($leaguePlayers) : [];

        $availableDrivers = array_filter($drivers, function ($driver) use ($takenDrivers) {
            return in_array($driver, $takenDrivers) ? false : true;
        });

        /* It ensures that array will be indexed properly, it means there will not be indexes like 0, 1, 2, 5, 7, 8, 9, 11 */
        shuffle($availableDrivers);

        $random = rand(0, (count($availableDrivers) - 1));

        return $availableDrivers[$random];
    }

    public function getTakenDriversFromLeague(?object $leaguePlayers): array
    {
        $takenDrivers = array();

        foreach ($leaguePlayers as $player) {
            $takenDrivers[] = $player->getDriver();
        }

        return $takenDrivers;
    }
}
