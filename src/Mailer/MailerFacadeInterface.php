<?php

declare(strict_types=1);

namespace Mailer;

interface MailerFacadeInterface
{
    public function send(GenericEmail $email): void;
}
