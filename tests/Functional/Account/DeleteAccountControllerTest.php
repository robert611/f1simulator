<?php

declare(strict_types=1);

namespace Tests\Functional\Account;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Security\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Common\Fixtures;

final class DeleteAccountControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private Fixtures $fixtures;
    private UserRepository $userRepository;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
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

    #[Test]
    public function user_will_be_marked_as_deleted(): void
    {
        // given
        $user = $this->fixtures->aCustomUser(
            username: 'LuckyLuck',
            email: 'lucky.luck@gmail.com',
        );
        $this->client->loginUser($user);

        // when
        $crawler = $this->client->request('GET', '/account/delete-account/index');
        $form = $crawler->selectButton('Usuń konto')->form();
        $this->client->submit($form);

        // then
        self::assertResponseRedirects('/logout');

        // and then
        $refreshedUser = $this->userRepository->find($user->getId());
        self::assertTrue($refreshedUser->isDeleted());
    }
}
