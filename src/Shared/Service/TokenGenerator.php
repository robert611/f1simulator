<?php

declare(strict_types=1);

namespace Shared\Service;

final class TokenGenerator
{
    public static function bin2hex(int $length): string
    {
        return bin2hex(random_bytes($length));
    }
}
