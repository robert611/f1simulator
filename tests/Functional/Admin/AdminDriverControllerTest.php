<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use Domain\Repository\DriverRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

class AdminDriverControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private DriverRepository $driverRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->driverRepository = self::getContainer()->get(DriverRepository::class);
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
    public function admin_driver_new_form_can_be_displayed(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $this->fixtures->aTeamWithName('Ferrari');

        // when
        $this->client->request('GET', "/admin-driver/new");

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Dodanie nowego kierowcy');
        self::assertSelectorTextContains('body', 'Imię');
        self::assertSelectorTextContains('body', 'Nazwisko');
        self::assertSelectorTextContains('body', 'Numer samochodu');
        self::assertSelectorTextContains('body', 'Zespół');
    }

    #[Test]
    public function new_driver_car_number_must_be_unique(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // when
        $crawler = $this->client->request('GET', "/admin-driver/new");
        $form = $crawler->selectButton('Zapisz')->form([
            'driver[name]' => 'Lewis',
            'driver[surname]' => 'Hamilton',
            'driver[teamId]' => $teamFerrari->getId(),
            'driver[carNumber]' => $driver->getCarNumber(),
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Istnieje już kierowca z tym numerem samochodu');
        self::assertEquals(1, $this->driverRepository->count());
    }

    #[Test]
    public function new_driver_can_be_added(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');

        // when
        $crawler = $this->client->request('GET', "/admin-driver/new");
        $form = $crawler->selectButton('Zapisz')->form([
            'driver[name]' => 'Lewis',
            'driver[surname]' => 'Hamilton',
            'driver[teamId]' => $teamFerrari->getId(),
            'driver[carNumber]' => 88,
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-driver");

        // follow redirection
        $this->client->followRedirect();

        // and then
        self::assertSelectorTextContains('body', 'Dodano nowego kierowcę');
        self::assertEquals(1, $this->driverRepository->count());
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

    #[Test]
    public function admin_driver_edition_works(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $teamRedBull = $this->fixtures->aTeamWithName('RedBull');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // when
        $crawler = $this->client->request('GET', "/admin-driver/{$driver->getId()}/edit");
        $form = $crawler->selectButton('Zapisz')->form([
            'driver[name]' => 'Lewis',
            'driver[surname]' => 'Hamilton',
            'driver[teamId]' => $teamRedBull->getId(),
            'driver[carNumber]' => 88,
        ]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-driver/{$driver->getId()}/edit");

        // and then
        $driver = $this->driverRepository->find($driver->getId());
        self::assertSame('Lewis', $driver->getName());
        self::assertSame('Hamilton', $driver->getSurname());
        self::assertSame($teamRedBull->getId(), $driver->getTeam()->getId());
        self::assertSame(88, $driver->getCarNumber());
    }

    #[Test]
    public function delete_csrf_token_must_be_valid(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // when
        $this->client->request('DELETE', "/admin-driver/{$driver->getId()}", [
            '_token' => 'invalid_token',
        ]);

        // then
        self::assertResponseRedirects("/admin-driver");

        // and then
        self::assertEquals(1, $this->driverRepository->count());
    }

    #[Test]
    public function driver_cannot_be_used_to_be_deleted(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $driver = $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);
        $this->fixtures->aSeason($user, $driver);

        // and given
        $crawler = $this->client->request('GET', "/admin-driver");

        // when
        $form = $crawler->selectButton('Usuń')->form([]);
        $this->client->submit($form);

        // then
        self::assertResponseRedirects("/admin-driver");

        // follow redirection
        $this->client->followRedirect();

        // and then
        self::assertSelectorTextContains(
            '.alert-danger',
            'Kierowca nie może zostać usunięty ponieważ był użyty w istniejących sezonach'
        );

        // and then
        self::assertEquals(1, $this->driverRepository->count());
    }

    #[Test]
    public function driver_can_be_deleted(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // and given
        $teamFerrari = $this->fixtures->aTeamWithName('Ferrari');
        $this->fixtures->aDriver('Charles', 'Leclerc', $teamFerrari, 16);

        // and given
        $crawler = $this->client->request('GET', "/admin-driver");

        // when
        $form = $crawler->selectButton('Usuń')->form([]);
        $this->client->submit($form);

        // follow redirection
        $this->client->followRedirect();

        // and then
        self::assertSelectorTextContains(
            '.alert-success',
            'Kierowca został usunięty'
        );

        // and then
        self::assertEquals(0, $this->driverRepository->count());
    }
}
