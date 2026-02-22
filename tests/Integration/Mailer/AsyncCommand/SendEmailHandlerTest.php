<?php

declare(strict_types=1);

namespace Tests\Integration\Mailer\AsyncCommand;

use Mailer\AsyncCommand\SendEmail;
use Mailer\AsyncCommand\SendEmailHandler;
use Mailer\Service\MailerElasticLogger;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Tests\Common\Double\FakeFailingMailerTransport;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Mime\Email;

final class SendEmailHandlerTest extends KernelTestCase
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
        // given
        $command = new SendEmail(
            ['test@example.com'],
            'Welcome!',
            '@mailer/welcome_email/welcome_email_pl.html.twig',
            '@mailer/welcome_email/welcome_email_pl.txt.twig',
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Test]
    public function error_while_sending_email_will_be_logged(): void
    {
        // given
        $command = new SendEmail(
            ['test@example.com'],
            'Welcome!',
            '@mailer/welcome_email/welcome_email_pl.html.twig',
            '@mailer/welcome_email/welcome_email_pl.txt.twig',
            [
                'username' => 'John',
                'homepageUrl' => 'https://example.com',
            ],
        );

        // and given
        self::ensureKernelShutdown();
        self::bootKernel();
        self::getContainer()->set(MailerInterface::class, new Mailer(new FakeFailingMailerTransport()));

        // and given
        $logger = $this->createMock(MailerElasticLogger::class);
        $logger
            ->expects($this->once())
            ->method('error')
            ->with(
                ['test@example.com'],
                'Welcome!',
                $this->stringContains('FakeFailingMailerTransport'),
            );
        self::getContainer()->set(MailerElasticLogger::class, $logger);

        // and given
        $sendEmailHandler = self::getContainer()->get(SendEmailHandler::class);

        // when
        $sendEmailHandler->__invoke($command);
    }
}
