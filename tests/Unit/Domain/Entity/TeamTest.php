<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity;

use Domain\Entity\Driver;
use Domain\Entity\Team;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    #[Test]
    public function it_checks_if_driver_to_replace_will_be_drawn(): void
    {
        // given
        $team = Team::create("Mercedes", "mercedes.png");

        // and given
        $driverOne = Driver::create('Lewis', 'Hamilton', $team, 33);
        $driverTwo = Driver::create('Valteri', 'Bottas', $team, 5);

        // and given
        $team->addDriver($driverOne);
        $team->addDriver($driverTwo);

        // when
        $driverToBeReplaced = $team->drawDriverToReplace();

        // then
        self::assertContains($driverToBeReplaced, [$driverOne, $driverTwo]);
    }

    #[Test]
    public function it_checks_if_driver_to_replace_will_not_be_drawn_for_team_without_drivers(): void
    {
        // given
        $team = Team::create("Mercedes", "mercedes.png");

        // when
        $driverToBeReplaced = $team->drawDriverToReplace();

        // then
        self::assertNull($driverToBeReplaced);
    }
}
