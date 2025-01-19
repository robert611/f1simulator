<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Driver;
use App\Entity\Season;
use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    #[Test]
    public function canCreateSeason(): void
    {
        // given
        $user = new User();

        // and given
        $driver = new Driver();

        // when
        $season = Season::create($user, $driver);

        // then
        self::assertEquals($user, $season->getUser());
        self::assertEquals($driver, $season->getDriver());
        self::assertFalse($season->getCompleted());
    }

    #[Test]
    public function canEndSeason(): void
    {
        // given
        $season = Season::create(new User(), new Driver());

        // when
        $season->endSeason();

        // then
        self::assertTrue($season->getCompleted());
    }
}
