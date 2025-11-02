<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Tests\Common\PrivateProperty;
use Domain\Entity\Driver;
use Domain\Entity\Team;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Security\Entity\User;

class UserSeasonTest extends TestCase
{
    #[Test]
    public function it_checks_if_league_drivers_ids_will_be_correctly_returned(): void
    {
        // given
        $team1 = Team::create("Mercedes", "mercedes.png");
        $team2 = Team::create("Ferrari", "ferrari.png");
        $team3 = Team::create("Red Bull", "red_bull.png");
        $team4 = Team::create("Toro rosso", "toro_rosso.png");

        PrivateProperty::set($team1, 'id', 1);
        PrivateProperty::set($team2, 'id', 2);
        PrivateProperty::set($team3, 'id', 3);
        PrivateProperty::set($team4, 'id', 4);

        // and given
        $driver1 = Driver::create('Lewis', 'Hamilton', $team1, 33);
        $driver2 = Driver::create('Yuki', 'Spider', $team1, 5);
        $driver3 = Driver::create('Mike', 'Ross', $team2, 9);
        $driver4 = Driver::create('Michael', 'Smith', $team2, 24);
        $driver5 = Driver::create('Greg', 'House', $team3, 31);
        $driver6 = Driver::create('John', 'Marcus', $team3, 50);
        $driver7 = Driver::create('Thomas', 'Jackson', $team4, 50);
        $driver8 = Driver::create('Taylor', 'Spears', $team4, 50);

        PrivateProperty::set($driver1, 'id', 1);
        PrivateProperty::set($driver2, 'id', 2);
        PrivateProperty::set($driver3, 'id', 3);
        PrivateProperty::set($driver4, 'id', 4);
        PrivateProperty::set($driver5, 'id', 5);
        PrivateProperty::set($driver6, 'id', 6);
        PrivateProperty::set($driver7, 'id', 7);
        PrivateProperty::set($driver8, 'id', 8);

        // and given
        $team1->addDriver($driver1);
        $team1->addDriver($driver2);
        $team2->addDriver($driver3);
        $team2->addDriver($driver4);
        $team3->addDriver($driver5);
        $team3->addDriver($driver6);
        $team4->addDriver($driver7);
        $team4->addDriver($driver8);

        // and given
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();
        $userSeason = UserSeason::create(
            "J783NMS092C",
            10,
            $user1,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $userSeasonPlayer1 = UserSeasonPlayer::create($userSeason, $user1, $driver1->getId());
        $userSeasonPlayer2 = UserSeasonPlayer::create($userSeason, $user2, $driver2->getId());
        $userSeasonPlayer3 = UserSeasonPlayer::create($userSeason, $user3, $driver4->getId());
        $userSeasonPlayer4 = UserSeasonPlayer::create($userSeason, $user4, $driver6->getId());

        $userSeason->addPlayer($userSeasonPlayer1);
        $userSeason->addPlayer($userSeasonPlayer2);
        $userSeason->addPlayer($userSeasonPlayer3);
        $userSeason->addPlayer($userSeasonPlayer4);

        // when
        $userSeasonTeams = $userSeason->getLeagueDriversIds();

        // then
        self::assertEquals(
            [
                $driver1->getId(),
                $driver2->getId(),
                $driver4->getId(),
                $driver6->getId()
            ],
            $userSeasonTeams,
        );
    }
}
