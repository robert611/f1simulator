<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class DeleteAccountControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    #[Test]
    public function index_page_renders_with_warning(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/account/delete-account/index');

        // then
        self::assertResponseIsSuccessful();

        // and then
        self::assertSelectorTextContains('body', 'Usunięcie konta jest nieodwracalne.');
    }
}
