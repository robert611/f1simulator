<?php

declare(strict_types=1);

namespace Tests\Integration\Service\DriverStatistics;

use Tests\Common\Fixtures;
use Computer\Service\DriverStatistics\DriverPoints;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DriverPointsTest extends KernelTestCase
{
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_checks_if_get_driver_points_returns_correct_value(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Kyle', 'Walker', $team, 8);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $track3 = $this->fixtures->aTrack('china', 'china.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);
        $race3 = $this->fixtures->aRace($track3, $season);
        $this->fixtures->aRaceResult(6, $race1, $driver1);
        $this->fixtures->aRaceResult(14, $race2, $driver1);
        $this->fixtures->aRaceResult(9, $race3, $driver1);
        $this->fixtures->aRaceResult(3, $race1, $driver2);
        $this->fixtures->aRaceResult(6, $race2, $driver2);
        $this->fixtures->aRaceResult(19, $race3, $driver2);

        // when
        $firstDriverPoints = DriverPoints::getDriverPoints($driver1, $season);
        $secondDriverPoints = DriverPoints::getDriverPoints($driver2, $season);

        // then
        self::assertEquals(10, $firstDriverPoints);
        self::assertEquals(23, $secondDriverPoints);
    }

    #[Test]
    public function it_checks_if_get_driver_points_by_race_returns_correct_value(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('John', 'Speed', $team, 55);
        $driver2 = $this->fixtures->aDriver('Kyle', 'Walker', $team, 8);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $track3 = $this->fixtures->aTrack('china', 'china.png');
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);
        $race3 = $this->fixtures->aRace($track3, $season);
        $raceResult1 = $this->fixtures->aRaceResult(6, $race1, $driver1);
        $raceResult2 = $this->fixtures->aRaceResult(14, $race2, $driver1);
        $raceResult3 = $this->fixtures->aRaceResult(9, $race3, $driver1);
        $raceResult4 = $this->fixtures->aRaceResult(3, $race1, $driver2);
        $raceResult5 = $this->fixtures->aRaceResult(6, $race2, $driver2);
        $raceResult6 = $this->fixtures->aRaceResult(19, $race3, $driver2);

        // then
        self::assertEquals(8, DriverPoints::getDriverPointsByRace($raceResult1));
        self::assertEquals(0, DriverPoints::getDriverPointsByRace($raceResult2));
        self::assertEquals(2, DriverPoints::getDriverPointsByRace($raceResult3));
        self::assertEquals(15, DriverPoints::getDriverPointsByRace($raceResult4));
        self::assertEquals(8, DriverPoints::getDriverPointsByRace($raceResult5));
        self::assertEquals(0, DriverPoints::getDriverPointsByRace($raceResult6));
    }
}
