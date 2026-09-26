<?php

declare(strict_types=1);

namespace Security\Service;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserIsBlockedException extends CustomUserMessageAccountStatusException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
