<?php

declare(strict_types=1);

namespace App\Tests\Integration\Service\Classification;

use App\Tests\Common\Fixtures;
use Multiplayer\Service\LeagueTeamsClassification;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueTeamsClassificationTest extends KernelTestCase
{
    private Fixtures $fixtures;
    private LeagueTeamsClassification $leagueTeamsClassification;

    public function setUp(): void
    {
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->leagueTeamsClassification = self::getContainer()->get(LeagueTeamsClassification::class);
    }

    #[Test]
    public function it_checks_if_teams_classification_will_be_build(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");
        $user1 = $this->fixtures->aCustomUser("klara", "klara@gmail.com");
        $user2 = $this->fixtures->aCustomUser("john", "john@gmail.com");
        $user3 = $this->fixtures->aCustomUser("liam", "liam@gmail.com");
        $user4 = $this->fixtures->aCustomUser("paul", "paul@gmail.com");

        // and given
        $team1 = $this->fixtures->aTeamWithName('Ferrari');
        $team2 = $this->fixtures->aTeamWithName('Mercedes');
        $team3 = $this->fixtures->aTeamWithName('Haas');
        $team4 = $this->fixtures->aTeamWithName('Mclaren');
        $this->fixtures->aTeamWithName('Aston Martin');

        // and given
        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $team1, 33);
        $driver2 = $this->fixtures->aDriver('Yuki', 'Spider', $team1, 5);
        $driver3 = $this->fixtures->aDriver('Mike', 'Ross', $team2, 9);
        $driver4 = $this->fixtures->aDriver('Michael', 'Smith', $team2, 24);
        $driver5 = $this->fixtures->aDriver('Greg', 'House', $team3, 31);
        $this->fixtures->aDriver('John', 'Marcus', $team3, 50);
        $this->fixtures->aDriver('Thomas', 'Jackson', $team4, 63);
        $this->fixtures->aDriver('Taylor', 'Spears', $team4, 25);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $userSeasonPlayer1 = $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $userSeasonPlayer2 = $this->fixtures->aUserSeasonPlayer($userSeason, $user1, $driver2);
        $userSeasonPlayer3 = $this->fixtures->aUserSeasonPlayer($userSeason, $user2, $driver3);
        $userSeasonPlayer4 = $this->fixtures->aUserSeasonPlayer($userSeason, $user3, $driver4);
        $userSeasonPlayer5 = $this->fixtures->aUserSeasonPlayer($userSeason, $user4, $driver5);
        $userSeasonPlayer1->assignClassificationProperties(75, 1);
        $userSeasonPlayer2->assignClassificationProperties(54, 2);
        $userSeasonPlayer3->assignClassificationProperties(45, 3);
        $userSeasonPlayer4->assignClassificationProperties(36, 4);
        $userSeasonPlayer5->assignClassificationProperties(30, 5);

        // and given
        $track1 = $this->fixtures->aTrack('silverstone', 'silverstone.png');
        $track2 = $this->fixtures->aTrack('belgium', 'belgium.png');
        $track3 = $this->fixtures->aTrack('dutch', 'dutch.png');

        // and given
        $race1 = $this->fixtures->aUserSeasonRace($track1, $userSeason);
        $race2 = $this->fixtures->aUserSeasonRace($track2, $userSeason);
        $race3 = $this->fixtures->aUserSeasonRace($track3, $userSeason);

        // and given
        $this->fixtures->aUserSeasonRaceResult(1, 25, $race1, $userSeasonPlayer1);
        $this->fixtures->aUserSeasonRaceResult(2, 18, $race1, $userSeasonPlayer2);
        $this->fixtures->aUserSeasonRaceResult(3, 15, $race1, $userSeasonPlayer3);
        $this->fixtures->aUserSeasonRaceResult(4, 12, $race1, $userSeasonPlayer4);
        $this->fixtures->aUserSeasonRaceResult(5, 10, $race1, $userSeasonPlayer5);

        $this->fixtures->aUserSeasonRaceResult(1, 25, $race2, $userSeasonPlayer1);
        $this->fixtures->aUserSeasonRaceResult(2, 18, $race2, $userSeasonPlayer2);
        $this->fixtures->aUserSeasonRaceResult(3, 15, $race2, $userSeasonPlayer3);
        $this->fixtures->aUserSeasonRaceResult(4, 12, $race2, $userSeasonPlayer4);
        $this->fixtures->aUserSeasonRaceResult(5, 10, $race2, $userSeasonPlayer5);

        $this->fixtures->aUserSeasonRaceResult(1, 25, $race3, $userSeasonPlayer1);
        $this->fixtures->aUserSeasonRaceResult(2, 18, $race3, $userSeasonPlayer2);
        $this->fixtures->aUserSeasonRaceResult(3, 15, $race3, $userSeasonPlayer3);
        $this->fixtures->aUserSeasonRaceResult(4, 12, $race3, $userSeasonPlayer4);
        $this->fixtures->aUserSeasonRaceResult(5, 10, $race3, $userSeasonPlayer5);

        // when
        $classification = $this->leagueTeamsClassification->getClassification($userSeason);

        // then (only 3 teams have players in the league)
        self::assertEquals(3, count($classification->getTeamsSeasonResults()));

        // and then (team1, ferrari is first)
        self::assertEquals(1, $classification->getTeamsSeasonResults()[0]->getPosition());
        self::assertEquals(129, $classification->getTeamsSeasonResults()[0]->getPoints());
        self::assertEquals($team1, $classification->getTeamsSeasonResults()[0]->getTeam());

        // and then (team2, mercedes is second)
        self::assertEquals(2, $classification->getTeamsSeasonResults()[1]->getPosition());
        self::assertEquals(81, $classification->getTeamsSeasonResults()[1]->getPoints());
        self::assertEquals($team2, $classification->getTeamsSeasonResults()[1]->getTeam());

        // and then (team3, hass is third)
        self::assertEquals(3, $classification->getTeamsSeasonResults()[2]->getPosition());
        self::assertEquals(30, $classification->getTeamsSeasonResults()[2]->getPoints());
        self::assertEquals($team3, $classification->getTeamsSeasonResults()[2]->getTeam());
    }
}
