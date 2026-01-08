<?php

declare(strict_types=1);

namespace Integration\Computer;

use Computer\ComputerFacade;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class ComputerFacadeTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private ComputerFacade $facade;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->facade = self::getContainer()->get(ComputerFacade::class);
    }

    #[Test]
    public function driver_cannot_be_safely_deleted_if_is_a_part_of_a_season(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

        // and given
        $this->fixtures->aSeason($user, $driver);

        // when
        $result = $this->facade->canDriverBeSafelyDeleted($driver->getId());

        // then
        self::assertFalse($result);
    }

    #[Test]
    public function driver_can_be_safely_deleted(): void
    {
        // given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

        // when
        $result = $this->facade->canDriverBeSafelyDeleted($driver->getId());

        // then
        self::assertTrue($result);
    }

    #[Test]
    public function driver_statistics_will_be_returned(): void
    {
        // given
        $user = $this->fixtures->aUser();

        // and given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

        // and given
        $this->fixtures->aSeason($user, $driver);
        $this->fixtures->aSeason($user, $driver);

        // when
        $result = $this->facade->getDriverStatistics($driver->getId());

        // then
        self::assertEquals(2, $result->getSeasonsPlayed());
    }
}
