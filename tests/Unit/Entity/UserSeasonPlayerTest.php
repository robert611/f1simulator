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

        $raceResult1 = UserSeasonRaceResult::create(1, 25, $race1, $userSeasonPlayer);
        $raceResult2 = UserSeasonRaceResult::create(2, 18, $race2, $userSeasonPlayer);
        $raceResult3 = UserSeasonRaceResult::create(4, 12, $race3, $userSeasonPlayer);
        $raceResult4 = UserSeasonRaceResult::create(1, 25, $race4, $userSeasonPlayer);

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
