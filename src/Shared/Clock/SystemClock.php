<?php

declare(strict_types=1);

namespace Shared\Clock;

use DateTimeImmutable;

final class SystemClock implements Clock
{
    public function now(string $string = 'now'): DateTimeImmutable
    {
        return new DateTimeImmutable($string);
    }
}
