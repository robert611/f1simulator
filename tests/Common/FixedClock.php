<?php

declare(strict_types=1);

namespace Tests\Common;

use DateTimeImmutable;
use Shared\Clock\Clock;

final class FixedClock implements Clock
{
    private static ?DateTimeImmutable $dateTime = null;

    public static function now(string $string = 'now'): DateTimeImmutable
    {
        if (null === self::$dateTime) {
            return new DateTimeImmutable($string);
        }

        if ('now' !== $string) {
            return self::$dateTime->modify($string);
        }

        return self::$dateTime;
    }

    public static function setNow(string $dateTime): DateTimeImmutable
    {
        self::$dateTime = new DateTimeImmutable($dateTime);

        return self::$dateTime;
    }
}
