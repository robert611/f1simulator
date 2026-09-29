<?php

declare(strict_types=1);

namespace Account\Form\ChangeEmail;

use Account\Validator\CurrentPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChangeEmailType extends AbstractType
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
            ->add('newEmail', EmailType::class, [
                'constraints' => [
                    new NotBlank(message: 'email.not_blank'),
                    new Email(message: 'email.invalid'),
                ],
                'attr' => [
                    'title' => $this->translator->trans('email.invalid', [], 'validators'),
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ChangeEmailTypeDTO::class,
        ]);
    }
}
