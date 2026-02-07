<?php

declare(strict_types=1);

namespace Tests\Integration\Multiplayer\Service;

use Domain\Contract\DTO\DriverDTO;
use Multiplayer\Service\DrawDriverToReplace;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class DrawDriverToReplaceTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private DrawDriverToReplace $driverToReplace;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->driverToReplace = self::getContainer()->get(DrawDriverToReplace::class);
    }

    #[Test]
    public function it_checks_if_user_league_driver_to_replace_will_be_returned(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser('john1', 'john1@gmail.com');
        $user2 = $this->fixtures->aCustomUser('john2', 'john2@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        $toroRosso = $this->fixtures->aTeamWithName('Toro Rosso');
        $driver3 = $this->fixtures->aDriver("Mika", "Haki", $toroRosso, 46);
        $driver4 = $this->fixtures->aDriver("Michael", "Stewart", $toroRosso, 47);

        // and given
        $userSeason = $this->fixtures->aUserSeason(
            "J783NMS092C",
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver3);

        // when
        $driverToReplace = $this->driverToReplace->getDriverToReplaceInUserLeague($userSeason);

        // then
        self::assertEquals(DriverDTO::fromEntity($driver4), $driverToReplace);
    }

    #[Test]
    public function it_checks_if_getting_user_league_driver_to_replace_will_handle_no_drivers(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $userSeason = $this->fixtures->aUserSeason(
            "J783NMS092C",
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // when
        $driverToReplace = $this->driverToReplace->getDriverToReplaceInUserLeague($userSeason);

        // then
        self::assertNull($driverToReplace);
    }

    #[Test]
    public function it_checks_if_getting_user_league_driver_to_replace_will_handle_no_available_drivers(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser('john1', 'john1@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        // and given
        $userSeason = $this->fixtures->aUserSeason(
            "J783NMS092C",
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);

        // when
        $driverToReplace = $this->driverToReplace->getDriverToReplaceInUserLeague($userSeason);

        // then
        self::assertNull($driverToReplace);
    }
}
