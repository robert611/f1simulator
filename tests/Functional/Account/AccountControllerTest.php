<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

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
    public function index_page_is_successful(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/index');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'LuckyLuck');
        self::assertSelectorTextContains('body', 'lucky.luck@gmail.com');
        self::assertSelectorTextContains('body', 'Polska');
        self::assertSelectorTextContains('body', $user->getCreatedAt()->format('d.m.Y H:i'));
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
