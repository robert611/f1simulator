<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function it_checks_if_index_page_is_successful(string $url): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', $url);

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function it_checks_if_current_season_will_be_properly_displayed(): void
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
        $ferrari = $this->fixtures->aTeamWithName('ferrari');
        $alfaRomeo = $this->fixtures->aTeamWithName('Alfa Romeo');
        $haas = $this->fixtures->aTeamWithName('Haas');
        $mclaren = $this->fixtures->aTeamWithName('Mclaren');
        $mercedes = $this->fixtures->aTeamWithName('Mercedes');
        $racingPoint = $this->fixtures->aTeamWithName('Racing Point');
        $redBull = $this->fixtures->aTeamWithName('Red Bull');
        $renualt = $this->fixtures->aTeamWithName('Renault');
        $toroRosso = $this->fixtures->aTeamWithName('Toro Rosso');
        $williams = $this->fixtures->aTeamWithName('Williams');

        // and given
        $driver1 = $this->fixtures->aDriver('Kyle', 'Walker', $ferrari, 8);
        $driver2 = $this->fixtures->aDriver('Kyle', 'Walker', $ferrari, 8);
        $driver3 = $this->fixtures->aDriver('Kyle', 'Walker', $alfaRomeo, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $alfaRomeo, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $haas, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $haas, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $mclaren, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $mclaren, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $mercedes, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $mercedes, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $racingPoint, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $racingPoint, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $redBull, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $redBull, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $renualt, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $renualt, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $toroRosso, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $toroRosso, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $williams, 8);
        $this->fixtures->aDriver('Kyle', 'Walker', $williams, 8);

        // and given
        $season = $this->fixtures->aSeason($user, $driver1);

        // and given
        $race1 = $this->fixtures->aRace($track1, $season);
        $race2 = $this->fixtures->aRace($track2, $season);
        $this->fixtures->aRace($track3, $season);
        $this->fixtures->aRace($track4, $season);
        $this->fixtures->aRace($track5, $season);

        // and given
        $this->fixtures->aRaceResult(1, $race1, $driver1);
        $this->fixtures->aRaceResult(2, $race1, $driver2);
        $this->fixtures->aRaceResult(3, $race1, $driver3);
        $this->fixtures->aRaceResult(1, $race2, $driver1);
        $this->fixtures->aRaceResult(2, $race2, $driver2);
        $this->fixtures->aRaceResult(3, $race2, $driver3);

        // when
        $this->client->request('GET', '/home/DRIVERS');

        // then
        self::assertResponseIsSuccessful();

        // and then (User has two first places and the next track will be Monaco)
        $this->assertSelectorTextContains('#first-place-podiums-count', '2');
        $this->assertSelectorTextContains('.table-responsive', $driver1->getFullName());
        $this->assertSelectorTextContains('.table-responsive', $driver2->getFullName());
        $this->assertSelectorTextContains('.table-responsive', $driver3->getFullName());
        $this->assertSelectorTextContains('.table-responsive', '50');
        $this->assertSelectorTextContains('.table-responsive', '36');
        $this->assertSelectorTextContains('.table-responsive', '30');
        $this->assertSelectorTextContains('body', $track6->getName());
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function it_checks_if_unlogged_user_will_be_redirected(string $url): void
    {
        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    #[Test]
    public function it_checks_if_empty_slash_will_redirect_to_homepage(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/');

        // then
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public static function provideUrls(): array
    {
        return [
            ['/home'],
            ['/home/RACE'],
            ['/home/DRIVERS'],
            ['/home/QUALIFICATIONS'],
        ];
    }
}
