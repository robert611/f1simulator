<?php

declare(strict_types=1);

namespace Tests\Unit\Computer\Entity;

use Computer\Entity\Race;
use Computer\Entity\RaceResult;
use Computer\Entity\Season;
use Domain\Entity\Driver;
use Domain\Entity\Team;
use Domain\Entity\Track;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;
use Tests\Common\PrivateProperty;

class SeasonTest extends TestCase
{
    #[Test]
    public function canCreateSeason(): void
    {
        // given
        $user = new User();

        // and given
        $driver = new Driver();
        PrivateProperty::set($driver, 'id', 1);

        // when
        $season = Season::create($user, $driver->getId());

        // then
        self::assertEquals($user, $season->getUser());
        self::assertEquals($driver->getId(), $season->getDriverId());
        self::assertFalse($season->getCompleted());
    }

    #[Test]
    public function canEndSeason(): void
    {
        // given
        $season = Season::create(new User(), 1);

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
        PrivateProperty::set($driver, 'id', 1);
        $season = Season::create(new User(), $driver->getId());

        // and given
        $track1 = Track::create('silverstone', 'silverstone.png');
        $track2 = Track::create('hungary', 'hungary.png');
        $track3 = Track::create('spain', 'spain.png');
        $track4 = Track::create('usa', 'usa.png');
        $track5 = Track::create('belgium', 'belgium.png');

        PrivateProperty::set($track1, 'id', 1);
        PrivateProperty::set($track2, 'id', 2);
        PrivateProperty::set($track3, 'id', 3);
        PrivateProperty::set($track4, 'id', 4);
        PrivateProperty::set($track5, 'id', 5);

        // and given
        $race1 = Race::create($track1->getId(), $season);
        $race2 = Race::create($track2->getId(), $season);
        $race3 = Race::create($track3->getId(), $season);
        $race4 = Race::create($track4->getId(), $season);
        $race5 = Race::create($track5->getId(), $season);
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
        $raceResult1 = RaceResult::create(1, $race1, $driver->getId());
        $raceResult2 = RaceResult::create(3, $race2, $driver->getId());
        $raceResult3 = RaceResult::create(0, $race3, $driver->getId());
        $raceResult4 = RaceResult::create(2, $race4, $driver->getId());
        $raceResult5 = RaceResult::create(2, $race5, $driver->getId());
        $race1->addRaceResult($raceResult1);
        $race2->addRaceResult($raceResult2);
        $race3->addRaceResult($raceResult3);
        $race4->addRaceResult($raceResult4);
        $race5->addRaceResult($raceResult5);

        // when
        $driverPodiumsDTO = $season->getDriverPodiumsDTO();

        // then
        self::assertEquals(1, $driverPodiumsDTO->getFirstPlacePodiums());
        self::assertEquals(2, $driverPodiumsDTO->getSecondPlacePodiums());
        self::assertEquals(1, $driverPodiumsDTO->getThirdPlacePodiums());
    }
}
