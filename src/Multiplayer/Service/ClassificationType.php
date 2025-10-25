<?php

declare(strict_types=1);

namespace Multiplayer\Service;

enum ClassificationType: string
{
    case RACE = 'RACE';
    case PLAYERS = 'PLAYERS';
    case QUALIFICATIONS = 'QUALIFICATIONS';
}
