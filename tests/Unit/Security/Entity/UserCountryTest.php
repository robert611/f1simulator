<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Entity;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\UserCountry;

final class UserCountryTest extends TestCase
{
    #[Test]
    public function it_returns_translated_labels(): void
    {
        // then
        self::assertSame('Polska', UserCountry::PL->getLabel('pl'));
        self::assertSame('Poland', UserCountry::PL->getLabel('en'));
        self::assertSame('Poland', UserCountry::PL->getLabel(''));

        // and then
        self::assertSame('Wielka Brytania', UserCountry::GB->getLabel('pl'));
        self::assertSame('United Kingdom', UserCountry::GB->getLabel('en'));
        self::assertSame('United Kingdom', UserCountry::GB->getLabel(''));

        // and then
        self::assertSame('Stany Zjednoczone', UserCountry::US->getLabel('pl'));
        self::assertSame('United States', UserCountry::US->getLabel('en'));
        self::assertSame('United States', UserCountry::US->getLabel(''));
    }
}
