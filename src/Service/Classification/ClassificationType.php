<?php

declare(strict_types=1);

namespace App\Service\Classification;

enum ClassificationType: string
{
    case DRIVERS = 'DRIVERS';
    case RACE = 'RACE';
    case PLAYERS = 'PLAYERS';
    case QUALIFICATIONS = 'QUALIFICATIONS';
}
