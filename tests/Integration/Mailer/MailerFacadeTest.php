<?php

declare(strict_types=1);

namespace Tests\Integration\Mailer;

use Mailer\AsyncCommand\SendEmail;
use Mailer\GenericEmail;
use Mailer\MailerFacade;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class MailerFacadeTest extends KernelTestCase
{
    private MailerFacade $mailerFacade;
    private InMemoryTransport $inMemoryTransport;

    protected function setUp(): void
    {
        $this->mailerFacade = self::getContainer()->get(MailerFacade::class);
        $this->inMemoryTransport = self::getContainer()->get('messenger.transport.async');
    }

    #[Test]
    public function email_will_be_dispatched(): void
    {
        // given
        $genericEmail = new GenericEmail(
            ['email@example.com'],
            'Test Email',
            '@mailer/welcome-email.html.twig',
            '@mailer/welcome-email.txt.twig',
            [
                'username' => 'Test User',
                'homepageUrl' => 'https://example.com/',
            ],
        );

        // when
        $this->mailerFacade->send($genericEmail);

        // then
        $sent = $this->inMemoryTransport->getSent();
        /** @var SendEmail $message */
        $message = $sent[0]->getMessage();
        self::assertCount(1, $sent);
        self::assertInstanceOf(SendEmail::class, $message);
        self::assertEquals($genericEmail->to, $message->to);
        self::assertEquals($genericEmail->subject, $message->subject);
        self::assertEquals($genericEmail->htmlTemplate, $message->htmlTemplate);
        self::assertEquals($genericEmail->plainTemplate, $message->plainTemplate);
        self::assertEquals($genericEmail->contentParams, $message->contentParams);
    }
}
