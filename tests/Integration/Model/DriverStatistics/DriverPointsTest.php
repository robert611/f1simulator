<?php

declare(strict_types=1);

namespace App\Tests\Integration\Model\DriverStatistics;

use App\Service\DriverStatistics\DriverPoints;
use App\Tests\Common\Fixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DriverPointsTest extends KernelTestCase 
{
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    public function test_if_get_driver_points_returns_correct_value(): void
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
        $this->fixtures->aRaceResult(3, $race3, $driver2);
        $this->fixtures->aRaceResult(6, $race3, $driver2);
        $this->fixtures->aRaceResult(19, $race3, $driver2);

        // when
        $firstDriverPoints = DriverPoints::getDriverPoints($driver1, $season);
        $secondDriverPoints = DriverPoints::getDriverPoints($driver2, $season);

        // then
        self::assertEquals(10, $firstDriverPoints);
        self::assertEquals(23, $secondDriverPoints);
    }
}
