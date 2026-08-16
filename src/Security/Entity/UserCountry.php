<?php

declare(strict_types=1);

namespace Security\Entity;

enum UserCountry: string
{
    case PL = 'PL';
    case GB = 'GB';
    case US = 'US';

    public function getLabel(string $locale): string
    {
        if ('pl' === $locale) {
            return $this->toPolish();
        }

        return $this->toEnglish();
    }

    public function toPolish(): string
    {
        return match ($this) {
            self::PL => 'Polska',
            self::GB => 'Wielka Brytania',
            self::US => 'Stany Zjednoczone',
        };
    }

    public function toEnglish(): string
    {
        return match ($this) {
            self::PL => 'Poland',
            self::GB => 'United Kingdom',
            self::US => 'United States',
        };
    }
}
