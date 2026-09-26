<?php

declare(strict_types=1);

namespace Account\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CurrentPassword extends Constraint
{
    public string $message = 'password.current_invalid';
}
