<?php

declare(strict_types=1);

namespace Security;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Security\Contract\UserDTO;
use Security\Repository\UserRepository;

final readonly class SecurityFacade implements SecurityFacadeInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return UserDTO[]
     */
    public function getUsers(): array
    {
        $users = $this->userRepository->findAll();

        return UserDTO::fromEntityCollection($users);
    }

    public function getUserById(int $id): ?UserDTO
    {
        $user = $this->userRepository->find($id);

        if (null === $user) {
            return null;
        }

        return UserDTO::fromEntity($user);
    }

    public function updateUser(int $id, bool $isVerified): void
    {
        $user = $this->userRepository->find($id);

        if (null === $user) {
            return;
        }

        $user->update($isVerified);
        $this->entityManager->flush();
    }
}
