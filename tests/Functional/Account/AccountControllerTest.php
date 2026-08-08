<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
}
