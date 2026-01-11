<?php

declare(strict_types=1);

namespace Tests\Functional\Multiplayer;

use Multiplayer\Repository\UserSeasonQualificationsRepository;
use Multiplayer\Repository\UserSeasonRaceResultsRepository;
use Tests\Common\Fixtures;
use Multiplayer\Repository\UserSeasonPlayersRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LeagueControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserSeasonPlayersRepository $userSeasonPlayerRepository;
    private UserSeasonQualificationsRepository $userSeasonQualificationsRepository;
    private UserSeasonRaceResultsRepository $userSeasonRaceResultsRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userSeasonPlayerRepository = self::getContainer()->get(UserSeasonPlayersRepository::class);
        $this->userSeasonQualificationsRepository = self::getContainer()->get(
            UserSeasonQualificationsRepository::class,
        );
        $this->userSeasonRaceResultsRepository = self::getContainer()->get(UserSeasonRaceResultsRepository::class);
    }

    #[Test]
    public function can_user_join_a_league(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

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

        // when
        $this->client->request('POST', '/league/join', ['league-secret' => $secret]);

        // then
        self::assertResponseRedirects('/multiplayer/');

        // and then
        self::assertEquals(1, $this->userSeasonPlayerRepository->count());

        // and then
        $userSeasonPlayer = $this->userSeasonPlayerRepository->findOneBy([]);
        self::assertEquals($userSeasonPlayer->getDriverId(), $driver->getId());
        self::assertEquals($userSeasonPlayer->getUser(), $user);
        self::assertEquals($userSeasonPlayer->getSeason(), $userSeason);
    }

    #[Test]
    public function will_trying_to_join_not_existing_league_be_handled(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $secret = "84NI0PKL32";

        // when
        $this->client->request('POST', '/league/join', ['league-secret' => $secret]);

        // then
        self::assertResponseRedirects('/home');
    }

    #[Test]
    public function user_cannot_join_league_twice(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

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
        $this->fixtures->aUserSeasonPlayer($userSeason, $user, $driver);

        // when
        $this->client->request('POST', '/league/join', ['league-secret' => $secret]);

        // then
        self::assertResponseRedirects('/home');

        // and then
        self::assertEquals(1, $this->userSeasonPlayerRepository->count([]));
    }

    #[Test]
    public function league_players_cannot_exceed_limit(): void
    {
        // given
        $loggedUser = $this->fixtures->aUser();
        $this->client->loginUser($loggedUser);

        // and given
        $leagueUser1 = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $leagueUser2 = $this->fixtures->aCustomUser('league_user_2', 'user2@gmail.com');

        // and given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);
        $driver2 = $this->fixtures->aDriver("Mark", "Evans", $team, 102);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            2,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser1, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser2, $driver2);

        // when
        $this->client->request('POST', '/league/join', ['league-secret' => $secret]);

        // then
        self::assertResponseRedirects('/home');

        // and then
        self::assertEquals(2, $this->userSeasonPlayerRepository->count([]));
    }

    #[Test]
    public function it_checks_if_will_race_be_simulated(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $user,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $user, $driver);

        // and given
        $this->fixtures->aTrack('silverstone', 'silverstone.png');

        // when
        $this->client->request('GET', "/league/{$userSeason->getId()}/simulate/race");

        // then
        self::assertResponseRedirects("/multiplayer/{$userSeason->getId()}/show");
        self::assertEquals(1, $this->userSeasonQualificationsRepository->count());
        self::assertEquals(1, $this->userSeasonRaceResultsRepository->count());
    }
}
