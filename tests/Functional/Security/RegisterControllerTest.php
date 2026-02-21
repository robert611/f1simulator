<?php

declare(strict_types=1);

namespace Tests\Functional\Security;

use Mailer\AsyncCommand\SendEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use Security\Repository\UserConfirmationRepository;
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
    private UserConfirmationRepository $userConfirmationRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->fixtures = self::getContainer()->get(Fixtures::class);
        $this->userRepository = self::getContainer()->get(UserRepository::class);
        $this->userConfirmationRepository = self::getContainer()->get(UserConfirmationRepository::class);
    }

    #[Test]
    public function logged_user_cannot_access_register_page(): void
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
            'registration_form[username]' => 'John5611',
            'registration_form[email]' => 'test@gmail.com',
            'registration_form[plainPassword][first]' => 'Password1...',
            'registration_form[plainPassword][second]' => 'Password1...',
            'registration_form[agreeTerms]' => true,
        ]);
        $this->client->submit($form);

        // then (User is created)
        $user = $this->userRepository->findOneBy([]);
        self::assertSame('John5611', $user->getUsername());
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

        // and then (User confirmation token was created)
        $userConfirmation = $this->userConfirmationRepository->findOneBy([]);
        self::assertEquals(1, $this->userConfirmationRepository->count());
        self::assertEquals($user, $userConfirmation->getUser());
    }

    #[Test]
    public function username_must_be_unique(): void
    {
        // given
        $this->fixtures->aCustomUser('legacy_fighter', 'legacy_fighter@email.com');

        // when
        $this->client->request('GET', '/register');
        $this->client->submitForm('submit_button', [
            'registration_form[username]' => 'legacy_fighter',
            'registration_form[email]' => 'test@gmail.com',
            'registration_form[plainPassword][first]' => 'Password1...',
            'registration_form[plainPassword][second]' => 'Password1...',
            'registration_form[agreeTerms]' => true,
        ]);

        // then
        self::assertSelectorTextContains('body', 'Konto z tą nazwą użytkownika już istnieje');

        // and then
        self::assertEquals(1, $this->userRepository->count());
    }

    #[Test]
    public function email_must_be_unique(): void
    {
        // given
        $this->fixtures->aCustomUser('legacy_fighter', 'legacy_fighter@email.com');

        // when
        $this->client->request('GET', '/register');
        $this->client->submitForm('submit_button', [
            'registration_form[username]' => 'original_username',
            'registration_form[email]' => 'legacy_fighter@email.com',
            'registration_form[plainPassword][first]' => 'Password1...',
            'registration_form[plainPassword][second]' => 'Password1...',
            'registration_form[agreeTerms]' => true,
        ]);

        // then
        self::assertSelectorTextContains('body', 'Ten adres e-mail jest już używany.');

        // and then
        self::assertEquals(1, $this->userRepository->count());
    }

    #[Test]
    #[DataProvider('unmetConstraintProvider')]
    public function unmet_constraint_will_be_discovered(array $data, string $expectedMessage): void
    {
        // when
        $this->client->request('GET', '/register');
        $this->client->submitForm('submit_button', $data);

        // then
        self::assertSelectorTextContains('body', $expectedMessage);

        // and then
        self::assertEquals(0, $this->userRepository->count());
    }

    public static function unmetConstraintProvider(): array
    {
        return [
            [
                [
                    'registration_form[username]' => '',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Proszę podać nazwę użytkownika',
            ],
            [
                [
                    'registration_form[username]' => 'short',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Nazwa użytkownika musi mieć co najmniej 8 znaków',
            ],
            [
                [
                    'registration_form[username]' => str_repeat('A', 65),
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Nazwa użytkownika może mieć maksymalnie 64 znaków',
            ],
            [
                [
                    'registration_form[username]' => 'support',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Nazwa użytkownika zawiera niedozwolone słowo',
            ],
            [
                [
                    // first two letters "а" is in cyrylica, homoglyph attack
                    'registration_form[username]' => 'ааdmin12356',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Nazwa użytkownika zawiera niedozwolone słowo',
            ],
            [
                [
                    'registration_form[username]' => 'michał8240',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Nazwa użytkownika zawiera niedozwolone słowo',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => '',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Proszę podać adres e-mail',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'invalid_email',
                    'registration_form[plainPassword][first]' => 'Password1...',
                    'registration_form[plainPassword][second]' => 'Password1...',
                    'registration_form[agreeTerms]' => true,
                ],
                'Proszę podać poprawny adres e-mail',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => '',
                    'registration_form[plainPassword][second]' => '',
                    'registration_form[agreeTerms]' => true,
                ],
                'Proszę wprowadzić hasło',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Ce5!',
                    'registration_form[plainPassword][second]' => 'Ce5!',
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło musi zawierać co najmniej 12 znaków',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => str_repeat('A1.', 23),
                    'registration_form[plainPassword][second]' => str_repeat('A1.', 23),
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło może mieć maksymalnie 64 znaków',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'abc1234567890.',
                    'registration_form[plainPassword][second]' => 'abc1234567890.',
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło musi zawierać co najmniej jedną wielką literę',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Abc1234567890',
                    'registration_form[plainPassword][second]' => 'Abc1234567890',
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło musi zawierać co najmniej jeden znak specjalny',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'john1doe3546A.',
                    'registration_form[plainPassword][second]' => 'john1doe3546A.',
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło nie może zawierać nazwy użytkownika ani adresu e-mail',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'test@gmail.comA',
                    'registration_form[plainPassword][second]' => 'test@gmail.comA',
                    'registration_form[agreeTerms]' => true,
                ],
                'Hasło nie może zawierać nazwy użytkownika ani adresu e-mail',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Mi35lik9..!klo9i',
                    'registration_form[plainPassword][second]' => 'Mi35lik9..!klo9iC',
                    'registration_form[agreeTerms]' => true,
                ],
                'Podane hasła się różnią',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Mi35lik9..!klo9i',
                    'registration_form[plainPassword][second]' => 'Mi35lik9..!klo9iC',
                ],
                'Musisz zaakceptować nasz regulamin',
            ],
            [
                [
                    'registration_form[username]' => 'john1doe3546',
                    'registration_form[email]' => 'test@gmail.com',
                    'registration_form[plainPassword][first]' => 'Mi35lik9..!klo9i',
                    'registration_form[plainPassword][second]' => 'Mi35lik9..!klo9iC',
                    'registration_form[agreeTerms]' => false,
                ],
                'Musisz zaakceptować nasz regulamin',
            ],
        ];
    }
}
