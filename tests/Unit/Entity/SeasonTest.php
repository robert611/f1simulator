<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Driver;
use App\Entity\Race;
use App\Entity\RaceResult;
use App\Entity\Season;
use App\Entity\Team;
use App\Entity\Track;
use App\Entity\User;
use App\Tests\Common\PrivateProperty;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SeasonTest extends TestCase
{
    #[Test]
    public function canCreateSeason(): void
    {
        // given
        $user = new User();

        // and given
        $driver = new Driver();

        // when
        $season = Season::create($user, $driver);

        // then
        self::assertEquals($user, $season->getUser());
        self::assertEquals($driver, $season->getDriver());
        self::assertFalse($season->getCompleted());
    }

    #[Test]
    public function canEndSeason(): void
    {
        // given
        $season = Season::create(new User(), new Driver());

        // when
        $season->endSeason();

        // then
        self::assertTrue($season->getCompleted());
    }

    #[Test]
    public function it_checks_if_driver_podiums_can_be_calculated(): void
    {
        // given
        $team = Team::create('ferrari', 'ferrari.png');
        $driver = Driver::create('John', 'Doe', $team, 54);
        $season = Season::create(new User(), $driver);

        // and given
        $track1 = Track::create('silverstone', 'silverstone.png');
        $track2 = Track::create('hungary', 'hungary.png');
        $track3 = Track::create('spain', 'spain.png');
        $track4 = Track::create('usa', 'usa.png');
        $track5 = Track::create('belgium', 'belgium.png');

        // and given
        $race1 = Race::create($track1, $season);
        $race2 = Race::create($track2, $season);
        $race3 = Race::create($track3, $season);
        $race4 = Race::create($track4, $season);
        $race5 = Race::create($track5, $season);
        PrivateProperty::set($race1, 'id', 1);
        PrivateProperty::set($race2, 'id', 2);
        PrivateProperty::set($race3, 'id', 3);
        PrivateProperty::set($race4, 'id', 4);
        PrivateProperty::set($race5, 'id', 5);
        $season->addRace($race1);
        $season->addRace($race2);
        $season->addRace($race3);
        $season->addRace($race4);
        $season->addRace($race5);

        // and given
        $raceResult1 = RaceResult::create(1, $race1, $driver);
        $raceResult2 = RaceResult::create(3, $race2, $driver);
        $raceResult3 = RaceResult::create(0, $race3, $driver);
        $raceResult4 = RaceResult::create(2, $race4, $driver);
        $raceResult5 = RaceResult::create(2, $race5, $driver);
        $race1->addRaceResult($raceResult1);
        $race2->addRaceResult($raceResult2);
        $race3->addRaceResult($raceResult3);
        $race4->addRaceResult($raceResult4);
        $race5->addRaceResult($raceResult5);
        $driver->addRaceResult($raceResult1);
        $driver->addRaceResult($raceResult2);
        $driver->addRaceResult($raceResult3);
        $driver->addRaceResult($raceResult4);
        $driver->addRaceResult($raceResult5);

        // when
        $driverPodiumsDTO = $season->getDriverPodiumsDTO();

        // then
        self::assertEquals(1, $driverPodiumsDTO->getFirstPlacePodiums());
        self::assertEquals(2, $driverPodiumsDTO->getSecondPlacePodiums());
        self::assertEquals(1, $driverPodiumsDTO->getThirdPlacePodiums());;
    }
}
