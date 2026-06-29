<?php

declare(strict_types=1);

namespace Domain\Contract;

use Domain\Contract\Exception\TrackCannotBeDeletedException;

interface TrackServiceFacadeInterface
{
    public function add(string $raceName, string $name, string $picture, string $latitude, string $longitude): void;

    public function update(
        int $trackId,
        string $raceName,
        string $name,
        ?string $picture,
        string $latitude,
        string $longitude,
    ): void;

    /**
     * @throws TrackCannotBeDeletedException
     */
    public function delete(int $trackId): void;
}
