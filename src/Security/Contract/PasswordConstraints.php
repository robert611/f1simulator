<?php

declare(strict_types=1);

namespace Security\Contract;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class PasswordConstraints
{
    /**
     * @return list<Constraint>
     */
    public static function createSymfonyValidation(): array
    {
        return [
            new NotBlank(
                message: 'password.not_blank',
            ),
            new Length(
                min: 12,
                max: 64,
                minMessage: 'password.too_short',
                maxMessage: 'password.too_long',
            ),
            new Regex(
                pattern: '/[A-Z]/',
                message: 'password.missing_uppercase',
            ),
            new Regex(
                pattern: '/[\W_]/',
                message: 'password.missing_special',
            ),
        ];
    }

    public static function createBrowserValidation(string $title): array
    {
        return [
            'minlength' => 12,
            'maxlength' => 64,
            'pattern' => '(?=.*[A-Z])(?=.*[\W_]).{12,64}',
            'title' => $title,
            'autocomplete' => 'new-password',
        ];
    }
}
