<?php

declare(strict_types=1);

namespace Computer\Service;

enum ClassificationType: string
{
    case DRIVERS = 'DRIVERS';
    case RACE = 'RACE';
    case QUALIFICATIONS = 'QUALIFICATIONS';
}
