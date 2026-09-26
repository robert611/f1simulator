<?php

declare(strict_types=1);

namespace Admin\Form;

use Security\Contract\UserDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class UserEditFormModel
{
    #[Assert\Type('boolean')]
    public bool $isVerified;

    #[Assert\Type('boolean')]
    public bool $isBlocked;

    public static function fromUser(UserDTO $userDTO): self
    {
        $model = new self();
        $model->isVerified = $userDTO->isVerified();
        $model->isBlocked = $userDTO->isBlocked();

        return $model;
    }
}
