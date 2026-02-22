<?php

declare(strict_types=1);

namespace Security\EventListener;

use Security\Event\UserRegisteredEvent;
use Security\Service\UserConfirmationService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class UserRegisteredListener
{
    public function __construct(
        private UserConfirmationService $userConfirmationService,
    ) {
    }

    public function __invoke(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        $this->userConfirmationService->sendConfirmationEmail($user->getId());
    }
}
