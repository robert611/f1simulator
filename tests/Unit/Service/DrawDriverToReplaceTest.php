<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Driver;
use App\Entity\Team;
use App\Service\DrawDriverToReplace;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DrawDriverToReplaceTest extends TestCase
{
    #[Test]
    public function canGetDriverToReplace(): void
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
        $service = new DrawDriverToReplace();

        $driverToBeReplaced = $service->getDriverToReplace($team);

        // then
        self::assertContains($driverToBeReplaced, [$driverOne, $driverTwo]);
    }
}
