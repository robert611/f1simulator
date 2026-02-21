<?php

declare(strict_types=1);

namespace Security\Service;

use Security\Entity\User;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        /** @var User $user */
        if (false === $user->isVerified()) {
            throw new UserNotConfirmedException('account_not_confirmed_checker', $user->getId());
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Do nothing
    }
}
