<?php

declare(strict_types=1);

namespace Multiplayer;

interface MultiplayerFacadeInterface
{
    /**
     * This method checks if a driver with given id is a part of any multiplayer season or any other activity
     * in this module, that makes it impossible to delete a driver without breaking database integrity
     */
    public function canDriverBeSafelyDeleted(int $driverId): bool;
}
