<?php

declare(strict_types=1);

namespace Account\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class EmailRemainsAvailable extends Constraint
{
    public string $message = 'email.unique';
}
