<?php

declare(strict_types=1);

namespace Tests\Functional\Multiplayer;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

class UserSeasonControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function multiplayer_dashboard_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // Dodanie dwóch lig @TODO
        // Dodanie kilku torów @TODO

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
        $this->client->request('GET', '/multiplayer');

        // then
        self::assertResponseIsSuccessful();
    }
}
