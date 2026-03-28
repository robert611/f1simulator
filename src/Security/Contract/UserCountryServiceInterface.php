<?php

declare(strict_types=1);

namespace Security\Contract;

interface UserCountryServiceInterface
{
    /**
     * @return array<string, array{country: string, users: int, percentageOfAllUsers: float}>
     */
    public function getUserCountryMapData(): array;
}
