<?php

declare(strict_types=1);

namespace Security\Event;

use Security\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class UserConfirmedEvent extends Event
{
    public function __construct(
        private readonly User $user,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
