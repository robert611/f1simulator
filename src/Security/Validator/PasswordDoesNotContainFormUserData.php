<?php

declare(strict_types=1);

namespace Security\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordDoesNotContainFormUserData extends Constraint
{
    public string $message = 'password.contains_user_data';
}
