<?php

declare(strict_types=1);

namespace Account\Validator;

use Security\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PasswordDoesNotContainUserDataValidator extends ConstraintValidator
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordDoesNotContainUserData) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        $username = $user->getUsername();
        $email = $user->getEmail();

        if ((stripos($value, $username) !== false) || (stripos($value, $email) !== false)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
