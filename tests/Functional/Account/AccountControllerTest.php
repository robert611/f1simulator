<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use HttpResponse;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Common\Fixtures;

class AccountControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function homepage_is_successful(): void
    {
        // given
        $user = $this->fixtures->anAdmin();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/index');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function only_logged_user_can_access_account_page(): void
    {
        // when
        $this->client->request('GET', '/account/index');

        // then
        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
        self::assertResponseRedirects('/login');
    }
}
