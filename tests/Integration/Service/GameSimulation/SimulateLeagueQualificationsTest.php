<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\GameSimulation;

use App\Service\GameSimulation\SimulateLeagueQualifications;
use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateLeagueQualificationsTest extends KernelTestCase
{
    private Fixtures $fixtures;

    private SimulateLeagueQualifications $simulateLeagueQualifications;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->simulateLeagueQualifications = self::getContainer()->get(SimulateLeagueQualifications::class);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_team_has_no_drivers_in_league(): void
    {
        // given
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $driver = $this->fixtures->aDriver('Max', 'Verstappen', $redBull, 33);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam($mercedes->getName(), [$driver], []);

        // then
        self::assertNull($result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_unfinished_single_driver(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 16);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam('Ferrari', [$driver], []);

        // then
        self::assertSame($driver, $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_single_driver_already_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Carlos', 'Sainz', $team, 55);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam('Ferrari', [$driver], [$driver]);

        // then
        self::assertNull($result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_one_of_drivers_when_both_unfinished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('McLaren');
        $driver1 = $this->fixtures->aDriver('Lando', 'Norris', $team, 4);
        $driver2 = $this->fixtures->aDriver('Oscar', 'Piastri', $team, 81);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam('McLaren', [$driver1, $driver2], []);

        // then
        self::assertTrue(in_array($result, [$driver1, $driver2], true));
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_unfinished_driver_when_only_one_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Aston Martin');
        $driver1 = $this->fixtures->aDriver('Fernando', 'Alonso', $team, 14);
        $driver2 = $this->fixtures->aDriver('Lance', 'Stroll', $team, 18);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam(
            'Aston Martin',
            [$driver1, $driver2],
            [$driver1],
        );

        // then
        self::assertSame($driver2, $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_is_case_insensitive_by_team_name(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('AlphaTauri');
        $driver = $this->fixtures->aDriver('Yuki', 'Tsunoda', $team, 22);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam('alphatauri', [$driver], []);

        // then
        self::assertSame($driver, $result);
    }

    #[Test]
    public function it_checks_if_drawing_driver_returns_null_when_both_team_drivers_finished(): void
    {
        // given
        $team = $this->fixtures->aTeamWithName('Williams');
        $driver1 = $this->fixtures->aDriver('Alex', 'Albon', $team, 23);
        $driver2 = $this->fixtures->aDriver('Logan', 'Sargeant', $team, 2);

        // when
        $result = $this->simulateLeagueQualifications->drawDriverFromATeam(
            'Williams',
            [$driver1, $driver2],
            [$driver1, $driver2],
        );

        // then
        self::assertNull($result);
    }
}
