<?php

declare(strict_types=1);

namespace Security\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\FormInterface;

class PasswordDoesNotContainUserDataValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordDoesNotContainUserData) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var FormInterface $form */
        $form = $this->context->getRoot(); // Root form (registrationForm)
        $username = $form->get('username')->getData();
        $email = $form->get('email')->getData();

        if (
            (is_string($username) && stripos($value, $username) !== false) ||
            (is_string($email) && stripos($value, $email) !== false)
        ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
