<?php

declare(strict_types=1);

namespace Integration\Computer\Service\TeamStatistics;

use Computer\Service\TeamStatistics\TeamPoints;
use Domain\Contract\DTO\TeamDTO;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Common\Fixtures;

class TeamPointsTest extends KernelTestCase
{
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_checks_if_get_team_points_returns_correct_value(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Mike', 'Ross', $team, 80);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);
        $this->fixtures->aRaceResult(5, $race1, $driver1);
        $this->fixtures->aRaceResult(9, $race1, $driver2);
        $this->fixtures->aRaceResult(2, $race2, $driver1);
        $this->fixtures->aRaceResult(3, $race2, $driver2);

        // when
        $points = TeamPoints::getTeamPoints(TeamDTO::fromEntity($team), $season);

        // then
        self::assertEquals(45, $points);
    }
}
