<?php

declare(strict_types=1);

namespace Account\Form\ChangePassword;

use Account\Validator\CurrentPassword;
use Account\Validator\PasswordDoesNotContainUserData;
use Security\Contract\PasswordConstraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChangePasswordType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'password.not_blank'),
                    new CurrentPassword(),
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'password.mismatch',
                'constraints' => array_merge(
                    PasswordConstraints::createSymfonyValidation(),
                    [
                        new PasswordDoesNotContainUserData(),
                    ],
                ),
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => PasswordConstraints::createBrowserValidation(
                        $this->translator->trans('password.front_validation', [], 'validators'),
                    ),
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => PasswordConstraints::createBrowserValidation(
                        $this->translator->trans('password.front_validation', [], 'validators'),
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangePasswordTypeDTO::class,
        ]);
    }
}
