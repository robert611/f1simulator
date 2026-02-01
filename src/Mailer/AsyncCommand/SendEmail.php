<?php

declare(strict_types=1);

namespace Mailer\AsyncCommand;

use Shared\Messenger\EmailQueue;

final readonly class SendEmail implements EmailQueue
{
    public function __construct(
        public array $to,
        public string $subject,
        public string $htmlTemplate,
        public string $plainTemplate,
        public array $contentParams,
    ) {
    }
}
