<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

class AdminDriverControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function admin_driver_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/admin-driver');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function admin_driver_index_displays_all_drivers(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('Red Bull');
        $teamMercedes = $this->fixtures->aTeamWithName('Mercedes');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $driver2 = $this->fixtures->aDriver('Carlos', 'Sainz', $teamFerrari, 55);
        $driver3 = $this->fixtures->aDriver('Max', 'Verstappen', $teamRedBull, 33);
        $driver4 = $this->fixtures->aDriver('Sergio', 'Perez', $teamRedBull, 11);
        $driver5 = $this->fixtures->aDriver('Lewis', 'Hamilton', $teamMercedes, 44);
        $driver6 = $this->fixtures->aDriver('George', 'Russell', $teamMercedes, 63);

        // when
        $this->client->request('GET', '/admin-driver');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', $driver1->getName());
        self::assertSelectorTextContains('body', $driver1->getSurname());
        self::assertSelectorTextContains('body', $driver2->getName());
        self::assertSelectorTextContains('body', $driver2->getSurname());
        self::assertSelectorTextContains('body', $driver3->getName());
        self::assertSelectorTextContains('body', $driver3->getSurname());
        self::assertSelectorTextContains('body', $driver4->getName());
        self::assertSelectorTextContains('body', $driver4->getSurname());
        self::assertSelectorTextContains('body', $driver5->getName());
        self::assertSelectorTextContains('body', $driver5->getSurname());
        self::assertSelectorTextContains('body', $driver6->getName());
        self::assertSelectorTextContains('body', $driver6->getSurname());
    }

    #[Test]
    public function admin_driver_edit_form_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver1 = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // when
        $this->client->request('GET', "/admin-driver/{$driver1->getId()}/edit");

        // then
        self::assertResponseIsSuccessful();
        $crawler = $this->client->getCrawler();

        // and then
        self::assertSame(
            $driver1->getName(),
            $crawler->filter('input[name="driver[name]"]')->attr('value'),
        );
        self::assertSame(
            $driver1->getSurname(),
            $crawler->filter('input[name="driver[surname]"]')->attr('value'),
        );
        self::assertSame(
            (string) $driver1->getCarNumber(),
            $crawler->filter('input[name="driver[carNumber]"]')->attr('value'),
        );
        $selectedTeamOption = $crawler
            ->filter('select[name="driver[teamId]"] option[selected]')
            ->first();
        self::assertSame(
            $driver1->getTeam()->getName(),
            trim($selectedTeamOption->text())
        );
    }
}
