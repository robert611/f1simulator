<?php

declare(strict_types=1);

namespace Tests\Common;

use DateTimeImmutable;
use Shared\Clock\Clock;

final class FixedClock implements Clock
{
    private ?DateTimeImmutable $dateTime = null;

    public function now(string $string = 'now'): DateTimeImmutable
    {
        if (null === $this->dateTime) {
            return new DateTimeImmutable($string);
        }

        if ('now' !== $string) {
            return $this->dateTime->modify($string);
        }

        return $this->dateTime;
    }

    public function setNow(string $dateTime): DateTimeImmutable
    {
        $this->dateTime = new DateTimeImmutable($dateTime);

        return $this->dateTime;
    }
}
