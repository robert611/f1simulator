<?php

declare(strict_types=1);

namespace Tests\Functional\Multiplayer;

use Multiplayer\Repository\UserSeasonPlayersRepository;
use Multiplayer\Repository\UserSeasonQualificationsRepository;
use Multiplayer\Repository\UserSeasonRaceResultsRepository;
use Multiplayer\Repository\UserSeasonRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

class UserSeasonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserSeasonRepository $userSeasonRepository;
    private UserSeasonPlayersRepository $userSeasonPlayerRepository;
    private UserSeasonQualificationsRepository $userSeasonQualificationsRepository;
    private UserSeasonRaceResultsRepository $userSeasonRaceResultsRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userSeasonRepository = self::getContainer()->get(UserSeasonRepository::class);
        $this->userSeasonPlayerRepository = self::getContainer()->get(UserSeasonPlayersRepository::class);
        $this->userSeasonQualificationsRepository = self::getContainer()->get(
            UserSeasonQualificationsRepository::class,
        );
        $this->userSeasonRaceResultsRepository = self::getContainer()->get(UserSeasonRaceResultsRepository::class);
    }

    #[Test]
    public function user_season_show_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $track1 = $this->fixtures->aTrack('Australian Grand Prix', 'australia.png');
        $track2 = $this->fixtures->aTrack('Bahrain Grand Prix', 'bahrain.png');
        $track3 = $this->fixtures->aTrack('China Grand Prix', 'chinese.png');
        $track4 = $this->fixtures->aTrack('Azerbaijan Grand Prix', 'azerbaijan.png');
        $track5 = $this->fixtures->aTrack('Spain Grand Prix', 'spanish.png');
        $track6 = $this->fixtures->aTrack('Monaco Grand Prix', 'monaco.png');

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
        $this->fixtures->aUserSeasonRace($track1->getId(), $userSeason);
        $this->fixtures->aUserSeasonRace($track2->getId(), $userSeason);
        $this->fixtures->aUserSeasonRace($track3->getId(), $userSeason);
        $this->fixtures->aUserSeasonRace($track4->getId(), $userSeason);
        $this->fixtures->aUserSeasonRace($track5->getId(), $userSeason);
        $this->fixtures->aUserSeasonRace($track6->getId(), $userSeason);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/show");

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function new_league_can_be_created(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $team = $this->fixtures->aTeam();
        $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

        // when
        $crawler = $this->client->request('GET', '/multiplayer');
        $form = $crawler->selectButton('Stwórz')->form([
            'user_season[name]' => 'Liga szybkich kierowców',
            'user_season[maxPlayers]' => '20',
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/multiplayer');

        // and then
        $userSeason = $this->userSeasonRepository->findOneBy([]);
        self::assertEquals(1, $this->userSeasonRepository->count());
        self::assertEquals("Liga szybkich kierowców", $userSeason->getName());
        self::assertEquals(20, $userSeason->getMaxPlayers());
        self::assertEquals(1, $userSeason->getPlayers()->count());
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
        $this->client->request('POST', '/player-league/join', ['user_season_secret' => $secret]);

        // then
        self::assertResponseRedirects('/multiplayer');

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
        $this->client->request('POST', '/player-league/join', ['user_season_secret' => $secret]);

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
        $this->client->request('POST', '/player-league/join', ['user_season_secret' => $secret]);

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
        $this->client->request('POST', '/player-league/join', ['user_season_secret' => $secret]);

        // then
        self::assertResponseRedirects('/home');

        // and then
        self::assertEquals(2, $this->userSeasonPlayerRepository->count([]));
    }

    #[Test]
    public function will_lack_of_drivers_when_joining_be_handled(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $owner = $this->fixtures->aCustomUser("marcin", "marcin@gmail.com");

        // and given
        $secret = "J783NMS092C";
        $this->fixtures->aUserSeason(
            $secret,
            10,
            $owner,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // when
        $this->client->request('POST', '/player-league/join', ['user_season_secret' => $secret]);

        // then
        self::assertResponseRedirects('/multiplayer');

        // follow redirection
        $this->client->followRedirect();

        // and then
        self::assertSelectorTextContains('.alert-warning', 'Brakuje kierowców, w których możesz się wcielić.');
    }

    #[Test]
    public function can_league_be_started(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $leagueUser2 = $this->fixtures->aCustomUser('league_user_2', 'user2@gmail.com');
        $this->client->loginUser($owner);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);
        $driver2 = $this->fixtures->aDriver("Mark", "Evans", $team, 102);

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
        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser2, $driver2);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/start");

        // then
        self::assertResponseRedirects("/player-league/{$userSeason->getId()}/show");

        // then
        self::assertTrue($userSeason->getStarted());
    }

    #[Test]
    public function only_owner_can_start_the_league(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $leagueUser2 = $this->fixtures->aCustomUser('league_user_2', 'user2@gmail.com');
        $this->client->loginUser($owner);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);
        $driver2 = $this->fixtures->aDriver("Mark", "Evans", $team, 102);

        // and given
        $secret = "J783NMS092C";
        $userSeason = $this->fixtures->aUserSeason(
            $secret,
            10,
            $leagueUser2,
            "Liga szybkich kierowców",
            false,
            false,
        );

        // and given
        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser2, $driver2);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/start");

        // then
        self::assertResponseRedirects("/home");

        // then
        self::assertFalse($userSeason->getStarted());
    }

    #[Test]
    public function league_must_have_at_least_two_players_to_start(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $this->client->loginUser($owner);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);

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
        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/start");

        // then
        self::assertResponseRedirects("/home");

        // then
        self::assertFalse($userSeason->getStarted());
    }

    #[Test]
    public function can_league_be_ended(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $leagueUser2 = $this->fixtures->aCustomUser('league_user_2', 'user2@gmail.com');
        $this->client->loginUser($owner);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);
        $driver2 = $this->fixtures->aDriver("Mark", "Evans", $team, 102);

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
        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser2, $driver2);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/end");

        // then
        self::assertResponseRedirects("/player-league/{$userSeason->getId()}/show");

        // then
        self::assertTrue($userSeason->getCompleted());
    }

    #[Test]
    public function only_owner_can_end_the_league(): void
    {
        // given
        $owner = $this->fixtures->aCustomUser('league_user_1', 'user1@gmail.com');
        $leagueUser2 = $this->fixtures->aCustomUser('league_user_2', 'user2@gmail.com');
        $this->client->loginUser($leagueUser2);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver("Lewis", "Hamilton", $team, 44);
        $driver2 = $this->fixtures->aDriver("Mark", "Evans", $team, 102);

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
        $this->fixtures->aUserSeasonPlayer($userSeason, $owner, $driver1);
        $this->fixtures->aUserSeasonPlayer($userSeason, $leagueUser2, $driver2);

        // when
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/end");

        // then
        self::assertResponseRedirects("/home");

        // then
        self::assertFalse($userSeason->getCompleted());
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
        $this->client->request('GET', "/player-league/{$userSeason->getId()}/simulate/race");

        // then
        self::assertResponseRedirects("/player-league/{$userSeason->getId()}/show");
        self::assertEquals(1, $this->userSeasonQualificationsRepository->count());
        self::assertEquals(1, $this->userSeasonRaceResultsRepository->count());
    }
}
