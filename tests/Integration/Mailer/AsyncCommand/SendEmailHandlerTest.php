<?php

declare(strict_types=1);

namespace Tests\Integration\Mailer\AsyncCommand;

use Mailer\AsyncCommand\SendEmail;
use Mailer\AsyncCommand\SendEmailHandler;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Mime\Email;

class SendEmailHandlerTest extends KernelTestCase
{
    private SendEmailHandler $sendEmailHandler;

    protected function setUp(): void
    {
        $this->sendEmailHandler = self::getContainer()->get(SendEmailHandler::class);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Test]
    public function email_is_sent(): void
    {
        $command = new SendEmail(
            ['test@example.com'],
            'Welcome!',
            '@mailer/welcome-email.html.twig',
            '@mailer/welcome-email.txt.twig',
            [
                'username' => 'John',
                'homepageUrl' => 'https://example.com',
            ],
        );

        // when
        $this->sendEmailHandler->__invoke($command);
        /** @var Email $email */
        $email = self::getMailerMessage();

        // then
        self::assertEmailCount(1);
        self::assertSame('test@example.com', $email->getTo()[0]->getAddress());
        self::assertSame('Welcome!', $email->getSubject());
        self::assertSame('f1simulator@example.com', $email->getFrom()[0]->getAddress());
        self::assertStringContainsString('John', $email->getTextBody());
    }
}
