<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\GameSimulation;

use App\Tests\Common\Fixtures;
use Multiplayer\Service\GameSimulation\SimulateLeagueRace;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateLeagueRaceTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private SimulateLeagueRace $simulateLeagueRace;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateLeagueRace = self::getContainer()->get(SimulateLeagueRace::class);
    }

    #[Test]
    public function it_simulates_league_race(): void
    {
        // given
        $userSeasonOwner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser('johnXT', 'johnxt@gmail.com');
        $user2 = $this->fixtures->aCustomUser('maria212', 'maria212@gmail.com');

        // and given
        $ferrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver("John", "Smith", $ferrari, 44);
        $driver2 = $this->fixtures->aDriver("Alex", "Apollo", $ferrari, 45);

        // and given
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver3 = $this->fixtures->aDriver("Yuki", "Grieg", $redBull, 46);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $userSeasonOwner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $userSeasonOwner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);
        $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver3);

        // when
        $result = $this->simulateLeagueRace->simulateRaceResults($userSeason);

        // then
        self::assertCount(3, $result->getQualificationsResults()->toPlainArray());
        self::assertCount(3, $result->getRaceResults());
    }
}
