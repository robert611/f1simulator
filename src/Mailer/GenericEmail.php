<?php

declare(strict_types=1);

namespace Mailer;

final readonly class GenericEmail
{
    public function __construct(
        public array $to,
        public string $subject,
        public string $htmlTemplate,
        public string $plainTemplate,
        public array $contentParams = [],
    ) {
    }
}
