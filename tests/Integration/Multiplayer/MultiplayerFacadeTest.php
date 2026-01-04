<?php

declare(strict_types=1);

namespace Integration\Multiplayer;

use Multiplayer\MultiplayerFacade;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class MultiplayerFacadeTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private MultiplayerFacade $facade;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->facade = self::getContainer()->get(MultiplayerFacade::class);
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
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $user,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $user, $driver);

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
}
