<?php

declare(strict_types=1);

namespace Security;

use Security\Contract\UserDTO;
use Security\Repository\UserRepository;

final readonly class SecurityFacade implements SecurityFacadeInterface
{
    public function __construct(
        private UserRepository $userRepository,
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
}
