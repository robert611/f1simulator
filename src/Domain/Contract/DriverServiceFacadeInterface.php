<?php

declare(strict_types=1);

namespace Domain\Contract;

use Domain\Contract\Exception\CarNumberTakenException;
use Domain\Contract\Exception\DriverCannotBeDeletedException;

interface DriverServiceFacadeInterface
{
    /**
     * @throws CarNumberTakenException
     */
    public function add(string $name, string $surname, int $teamId, int $carNumber): void;

    public function update(int $driverId, string $name, string $surname, int $teamId, int $carNumber): void;

    /**
     * @throws DriverCannotBeDeletedException
     */
    public function delete(int $driverId): void;
}
