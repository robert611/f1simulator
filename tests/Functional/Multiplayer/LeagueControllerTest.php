<?php

declare(strict_types=1);

namespace Tests\Functional\Multiplayer;

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

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userSeasonPlayerRepository = self::getContainer()->get(UserSeasonPlayersRepository::class);
    }

    #[Test]
    public function canJoin(): void
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
}
