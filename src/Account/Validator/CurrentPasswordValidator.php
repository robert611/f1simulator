<?php

declare(strict_types=1);

namespace Account\Validator;

use Security\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class CurrentPasswordValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly Security $security,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CurrentPassword) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
