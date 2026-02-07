<?php

declare(strict_types=1);

namespace Mailer;

use Mailer\AsyncCommand\SendEmail;
use Mailer\Contract\GenericEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final readonly class MailerFacade implements MailerFacadeInterface
{
    public function __construct(
        #[Autowire(service: 'monolog.logger.mailer')]
        private LoggerInterface $mailerLogger,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function send(GenericEmail $email): void
    {
        $command = new SendEmail(
            $email->to,
            $email->subject,
            $email->htmlTemplate,
            $email->plainTemplate,
            $email->contentParams,
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (Throwable $e) {
            $this->mailerLogger->error((string) $e);
        }
    }
}
