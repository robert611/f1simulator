<?php 

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Repository\SeasonRepository;
use App\Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private SeasonRepository $seasonRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->seasonRepository = self::getContainer()->get(SeasonRepository::class);
    }

    #[Test]
    public function canStartNewSeason(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $team = $this->fixtures->aTeamWithDrivers();

        // when
        $this->client->request('POST', '/game/season/start', ['teamId' => $team->getId()]);
        self::assertResponseRedirects('/home');

        // then (User will be redirected back to index page)
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        // and then (User season is created in the database)
        $dbSeason = $this->seasonRepository->findOneBy([]);
        self::assertEquals($dbSeason->getUser()->getId(), $user->getId());
        self::assertEquals($dbSeason->getDriver()->getTeam()->getId(), $team->getId());
        self::assertFalse($dbSeason->getCompleted());
    }

    #[Test]
    public function willStartingNewSeasonForATeamWithoutDriversBeHandled(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // and given
        $team = $this->fixtures->aTeam();

        // when
        $this->client->request('POST', '/game/season/start', ['teamId' => $team->getId()]);

        // then (User will be redirected back to index page)
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/home');

        // and then (User season was not created and is not in the database)
        self::assertEquals(0, $this->seasonRepository->count());

        // and then (Flash message is set)
        $errorFlashBags = $this->client->getRequest()->getSession()->getFlashBag()->get('error');
        self::assertCount(1, $errorFlashBags);
        self::assertEquals('Ten zespół nie posiada kierowców. Wybierz inny zespół.', $errorFlashBags[0]);
    }

    public function testEndSeason()
    {
        $user = $this->fixtures->getUser();

        $this->client->loginUser($user);

        $this->client->request('POST', '/game/season/end');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testSimulateRace()
    {
        $user = $this->fixtures->getUser();

        $this->client->loginUser($user);

        $this->client->request('POST', '/game/simulate/race');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('provideUrls')]
    public function testPagesInCaseOfUnloggedUser($url)
    {
        $this->client->request('GET', $url);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public static function provideUrls(): array
    {
        return [
            ['/game/season/start'],
            ['/game/season/end'],
            ['/game/simulate/race']
        ];
    }
}
