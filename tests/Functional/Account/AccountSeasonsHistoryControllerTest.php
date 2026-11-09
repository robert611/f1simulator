<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class AccountSeasonsHistoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function user_seasons_are_displayed_in_history(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // and given
        $team = $this->fixtures->aTeam();
        $driver1 = $this->fixtures->aDriver('Lewis', 'Hamilton', $team, 44);
        $driver2 = $this->fixtures->aDriver('Charles', 'Leclerc', $team, 11);

        // and given
        $userSeason = $this->fixtures->aSeason(
            $user,
            $driver1,
        );
        $userSeason->updatePosition(15);
        $userSeason->endSeason(new DateTimeImmutable('2025-10-10 20:00:15'));
        $this->fixtures->aSeason(
            $user,
            $driver2,
        );

        // when
        $crawler = $this->client->request('GET', '/account/seasons-history/index');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Historia sezonów');
        self::assertSelectorTextContains('tbody', '2025-10-10 20:00:15');
        self::assertEquals(2, $crawler->filter('tbody > tr')->count());
    }
}
