<?php

declare(strict_types=1);

namespace Account\Validator;

use Security\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EmailRemainsAvailableValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailRemainsAvailable) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        $queryResult = $this->userRepository->count(['email' => $value]);

        if ($queryResult > 0) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
