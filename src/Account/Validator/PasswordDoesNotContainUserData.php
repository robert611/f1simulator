<?php

declare(strict_types=1);

namespace Account\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class PasswordDoesNotContainUserData extends Constraint
{
    public string $message = 'password.contains_user_data';
}
