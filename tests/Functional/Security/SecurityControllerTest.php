<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tests\Common\Fixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private TokenStorageInterface $tokenStorage;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->tokenStorage = self::getContainer()->get(TokenStorageInterface::class);
    }

    #[Test]
    public function login_page_is_successful(): void
    {
        // when
        $this->client->request('GET', '/login');

        // then
        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function logged_user_cannot_access_login_page(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/login');

        // then
        self::assertResponseRedirects('/home');
    }

    #[Test]
    public function logging_out_works(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $this->client->loginUser($user);

        // when
        $this->client->request('GET', '/logout');

        // then
        self::assertResponseRedirects('/');

        // and then
        $this->client->followRedirect();
        self::assertNull($this->tokenStorage->getToken());
    }
}
