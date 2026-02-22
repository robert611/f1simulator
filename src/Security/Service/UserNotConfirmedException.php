<?php

declare(strict_types=1);

namespace Security\Service;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

final class UserNotConfirmedException extends CustomUserMessageAccountStatusException
{
    private int $userId;

    public function __construct(string $message, int $userId)
    {
        parent::__construct($message);
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
