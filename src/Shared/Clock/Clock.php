<?php

declare(strict_types=1);

namespace Shared\Clock;

use DateTimeImmutable;

interface Clock
{
    public function now(string $string = 'now'): DateTimeImmutable;
}
