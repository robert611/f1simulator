<?php

declare(strict_types=1);

namespace Security\Entity;

enum UserCountry: string
{
    case PL = 'PL';
    case GB = 'GB';
    case US = 'US';

    public function getLabel(): string
    {
        return match ($this) {
            self::PL => 'Poland',
            self::GB => 'United Kingdom',
            self::US => 'United States',
        };
    }
}
