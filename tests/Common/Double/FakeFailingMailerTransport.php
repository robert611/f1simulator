<?php

declare(strict_types=1);

namespace Tests\Common\Double;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;

final class FakeFailingMailerTransport implements TransportInterface
{
    public function __toString(): string
    {
        return 'Failing to send an email (For test purposes)';
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        throw new TransportException('SMTP failure (test)');
    }
}
