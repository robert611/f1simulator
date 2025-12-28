<?php

declare(strict_types=1);

namespace Tests\Functional\Admin;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

class AdminControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function admin_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/admin');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function only_user_with_admin_role_can_access_admin_page(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/admin');

        // then
        self::assertResponseRedirects('/home');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}
