<?php

declare(strict_types=1);

namespace Domain\Contract;

interface TrackServiceFacadeInterface
{
    public function add(string $name, string $picture): void;

    public function update(int $trackId, string $name, ?string $picture): void;
}
