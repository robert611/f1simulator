<?php

declare(strict_types=1);

namespace Account\Service;

use Doctrine\ORM\EntityManagerInterface;
use Security\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class ChangePasswordService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function changePassword(User $user, string $newPassword): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $newPassword,
            ),
        );

        $this->entityManager->flush();
    }
}
