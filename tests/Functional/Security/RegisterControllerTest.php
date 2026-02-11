<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use Mailer\AsyncCommand\SendEmail;
use Security\Repository\UserRepository;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Tests\Common\Fixtures;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
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

    #[Test]
    public function registration_form_works(): void
    {
        // when
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('Zarejestruj się')->form([
            'registration_form[username]' => 'John',
            'registration_form[email]' => 'test@gmail.com',
            'registration_form[plainPassword]' => 'password',
        ]);
        $this->client->submit($form);

        // then (User is created)
        $user = $this->userRepository->findOneBy([]);
        self::assertSame('John', $user->getUsername());
        self::assertSame('test@gmail.com', $user->getEmail());

        // and then (Welcome email is dispatched)
        /** @var InMemoryTransport $inMemoryTransport */
        $inMemoryTransport = self::getContainer()->get('messenger.transport.async');
        $messages = $inMemoryTransport->getSent();
        /** @var SendEmail $command */
        $command = $messages[0]->getMessage();
        self::assertCount(1, $messages);
        self::assertInstanceOf(SendEmail::class, $command);
        self::assertSame(['test@gmail.com'], $command->to);
    }
}
