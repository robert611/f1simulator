<?php 

namespace App\Service;

class DrawDriverToReplace
{
    /* This function takes team which user chose and draws one of the driver of that team to replace him with user */
    public function getDriverToReplace(object $team): object
    {
        $drivers = $team->getDrivers();

        $random = rand(0, (count($drivers) - 1));

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