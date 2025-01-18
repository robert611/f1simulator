<?php

declare(strict_types=1);

namespace App\Tests\Common;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class Fixtures
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function aUser(): User
    {
        $user = new User();
        $user->setUsername('tommy123');
        $user->setEmail('tommy123@gmail.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
