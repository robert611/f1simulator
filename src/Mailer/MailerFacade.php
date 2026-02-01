<?php

declare (strict_types=1);

namespace Mailer;

use Mailer\AsyncCommand\SendEmail;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final readonly class MailerFacade implements MailerFacadeInterface
{
    public function __construct(
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
        } catch (Throwable) {
            // @TODO Add file logger
        }
    }
}
