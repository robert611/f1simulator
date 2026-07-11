<?php

declare(strict_types=1);

namespace Security;

use Security\Contract\UserDTO;

interface SecurityFacadeInterface
{
    /**
     * @return UserDTO[]
     */
    public function getUsers(): array;
}
