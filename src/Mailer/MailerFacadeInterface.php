<?php

declare(strict_types=1);

namespace Mailer;

use Mailer\Contract\GenericEmail;

interface MailerFacadeInterface
{
    public function send(GenericEmail $email): void;
}
