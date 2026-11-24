<?php

declare(strict_types=1);

namespace OutgoingApi\Contract;

use DateTimeImmutable;
use DateTimeZone;

final class OutgoingApiDateTimeSerializer
{
    public static function serialize(?DateTimeImmutable $date): ?string
    {
        return $date
            ?->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d\TH:i:s\Z');
    }
}
