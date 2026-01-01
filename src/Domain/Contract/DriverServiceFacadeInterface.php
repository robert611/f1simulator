<?php

declare(strict_types=1);

namespace Domain\Contract;

interface DriverServiceFacadeInterface
{
    public function update(int $driverId, string $name, string $surname, int $teamId, int $carNumber): void;
}
