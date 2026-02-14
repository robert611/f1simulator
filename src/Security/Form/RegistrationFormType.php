<?php

declare(strict_types=1);

namespace Security\Form;

use Security\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'username.not_blank',
                    ),
                    new Length(
                        min: 8,
                        max: 64,
                        minMessage: 'username.min_length',
                        maxMessage: 'username.max_length',
                    ),
                    new Regex(
                        pattern: '/^(?!.*(admin|support|obsługa|moderator)).*$/i',
                        message: 'username.forbidden_words',
                    ),
                ],
                'attr' => [
                    'minlength' => 8,
                    'maxlength' => 64,
                    'pattern' => '.{8,64}',
                    'title' => $this->translator->trans('username.front_length_validation', [], 'validators'),
                ],
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(message: 'email.not_blank'),
                    new Email(message: 'email.invalid'),
                ],
                'attr' => [
                    'title' => $this->translator->trans('email.invalid', [], 'validators'),
                ],
                'required' => true,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Podaj poprawne hasło',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Twoje hasło powinno zawierać co najmniej {{ limit }} znaków',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => 'Hasło'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
