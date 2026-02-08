<?php

declare(strict_types=1);

namespace Mailer\Service;

use Psr\Log\LoggerInterface;

readonly class MailerElasticLogger
{
    public function __construct(
        private LoggerInterface $mailerElasticLogger,
    ) {
    }

    public function sent(array $to, string $subject, array $context = []): void
    {
        $this->mailerElasticLogger->info('Mail sent', [
            'status' => 'sent',
            'recipients' => $to,
            'subject' => $subject,
            ...$context,
        ]);
    }

    public function error(array $to, string $subject, string $error): void
    {
        $this->mailerElasticLogger->error('Mail error', [
            'status' => 'error',
            'to' => $to,
            'recipients' => $subject,
            'error' => $error,
        ]);
    }
}
