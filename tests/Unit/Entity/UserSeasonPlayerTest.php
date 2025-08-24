<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonRace;
use App\Entity\UserSeasonRaceResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserSeasonPlayerTest extends TestCase
{
    #[Test]
    public function it_checks_if_driver_podiums_can_be_calculated(): void
    {
        // given
        $userSeason = new UserSeason();

        $userSeasonPlayer = new UserSeasonPlayer();

        $userSeasonPlayer->setSeason($userSeason);

        $race1 = new UserSeasonRace();
        $race2 = new UserSeasonRace();
        $race3 = new UserSeasonRace();
        $race4 = new UserSeasonRace();

        $raceResult1 = new UserSeasonRaceResult();
        $raceResult1->setRace($race1);
        $raceResult1->setPlayer($userSeasonPlayer);
        $raceResult1->setPosition(1);

        $raceResult2 = new UserSeasonRaceResult();
        $raceResult2->setRace($race2);
        $raceResult2->setPlayer($userSeasonPlayer);
        $raceResult2->setPosition(2);

        $raceResult3 = new UserSeasonRaceResult();
        $raceResult3->setRace($race3);
        $raceResult3->setPlayer($userSeasonPlayer);
        $raceResult3->setPosition(4);

        $raceResult4 = new UserSeasonRaceResult();
        $raceResult4->setRace($race4);
        $raceResult4->setPlayer($userSeasonPlayer);
        $raceResult4->setPosition(1);

        $race1->addRaceResult($raceResult1);
        $race2->addRaceResult($raceResult2);
        $race3->addRaceResult($raceResult3);
        $race4->addRaceResult($raceResult4);

        $userSeason->addRace($race1);
        $userSeason->addRace($race2);
        $userSeason->addRace($race3);
        $userSeason->addRace($race4);

        $userSeasonPlayer->addRaceResult($raceResult1);
        $userSeasonPlayer->addRaceResult($raceResult2);
        $userSeasonPlayer->addRaceResult($raceResult3);
        $userSeasonPlayer->addRaceResult($raceResult4);

        // when
        $driverPodiumsDTO = $userSeasonPlayer->getDriverPodiumsDTO();

        // then
        self::assertEquals(2, $driverPodiumsDTO->getFirstPlacePodiums());
        self::assertEquals(1, $driverPodiumsDTO->getSecondPlacePodiums());
        self::assertEquals(0, $driverPodiumsDTO->getThirdPlacePodiums());
    }
}
