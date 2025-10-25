<?php

declare(strict_types=1);

namespace Tests\Controller;

use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function it_checks_if_unlogged_user_will_reach_login_page(): void
    {
        // when
        $this->client->request('GET', '/login');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function it_checks_if_logged_user_cannot_access_register_page(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/register');

        // then
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}
