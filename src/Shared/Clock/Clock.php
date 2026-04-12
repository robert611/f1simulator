<?php

declare(strict_types=1);

namespace Shared\Clock;

use DateTimeImmutable;

interface Clock
{
    public static function now(string $string = 'now'): DateTimeImmutable;
}
