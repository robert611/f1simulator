<?php

declare(strict_types=1);

namespace Account\Form\ChangePassword;

final class ChangePasswordTypeDTO
{
    public string $currentPassword;

    public string $newPassword;
}
