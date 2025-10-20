<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Tests\Common\PrivateProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\Entity\Driver;
use Domain\Entity\Team;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Entity\UserSeasonRace;
use Multiplayer\Entity\UserSeasonRaceResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;

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

    #[Test]
    public function it_checks_if_players_drivers_can_be_derived(): void
    {
        // given
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        // given
        $team1 = new Team();
        $team2 = new Team();

        // and given
        $driver1 = Driver::create('John', 'Doe', $team1, 10);
        $driver2 = Driver::create('Marta', 'Croft', $team1, 24);
        $driver3 = Driver::create('Filip', 'Masa', $team2, 9);
        $driver4 = Driver::create('Liam', 'Lawson', $team2, 98);

        // and given
        $userSeason = new UserSeason();

        // and given
        $userSeasonPlayer1 = UserSeasonPlayer::create($userSeason, $user1, $driver1);
        $userSeasonPlayer2 = UserSeasonPlayer::create($userSeason, $user2, $driver2);
        $userSeasonPlayer3 = UserSeasonPlayer::create($userSeason, $user3, $driver3);
        $userSeasonPlayer4 = UserSeasonPlayer::create($userSeason, $user4, $driver4);

        // when
        $drivers = UserSeasonPlayer::getPlayersDrivers(new ArrayCollection([
            $userSeasonPlayer1,
            $userSeasonPlayer2,
            $userSeasonPlayer3,
            $userSeasonPlayer4,
        ]));

        // then
        self::assertCount(4, $drivers);
        self::assertEquals($driver1, $drivers[0]);
        self::assertEquals($driver2, $drivers[1]);
        self::assertEquals($driver3, $drivers[2]);
        self::assertEquals($driver4, $drivers[3]);
    }

    #[Test]
    public function it_checks_if_player_will_be_returned_by_driver_id(): void
    {
        // given
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        // given
        $team1 = new Team();
        $team2 = new Team();

        // and given
        $driver1 = Driver::create('John', 'Doe', $team1, 10);
        $driver2 = Driver::create('Marta', 'Croft', $team1, 24);
        $driver3 = Driver::create('Filip', 'Masa', $team2, 9);
        $driver4 = Driver::create('Liam', 'Lawson', $team2, 98);

        // and given
        PrivateProperty::set($driver1, 'id', 1);
        PrivateProperty::set($driver2, 'id', 2);
        PrivateProperty::set($driver3, 'id', 3);
        PrivateProperty::set($driver4, 'id', 4);

        // and given
        $userSeason = new UserSeason();

        // and given
        $userSeasonPlayer1 = UserSeasonPlayer::create($userSeason, $user1, $driver1);
        $userSeasonPlayer2 = UserSeasonPlayer::create($userSeason, $user2, $driver2);
        $userSeasonPlayer3 = UserSeasonPlayer::create($userSeason, $user3, $driver3);
        $userSeasonPlayer4 = UserSeasonPlayer::create($userSeason, $user4, $driver4);

        // when
        $result = UserSeasonPlayer::getPlayerByDriverId(
            new ArrayCollection([
                $userSeasonPlayer1,
                $userSeasonPlayer2,
                $userSeasonPlayer3,
                $userSeasonPlayer4,
            ]),
            $driver3->getId(),
        );

        self::assertEquals($userSeasonPlayer3, $result);
    }
}
