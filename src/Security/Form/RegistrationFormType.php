<?php

declare(strict_types=1);

namespace Security\Form;

use Security\Contract\PasswordConstraints;
use Security\Entity\User;
use Security\Entity\UserCountry;
use Security\Validator\PasswordDoesNotContainFormUserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
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
        $locale = $this->translator->getLocale();

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
                        pattern: '/^(?!.*(?:admin|support|obsługa|moderator))[a-zA-Z0-9_\-]+$/iu',
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
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'password.mismatch',
                'constraints' => array_merge(
                    PasswordConstraints::createSymfonyValidation(),
                    [
                        new PasswordDoesNotContainFormUserData(),
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
            ])
            ->add('country', EnumType::class, [
                'class' => UserCountry::class,
                'choice_label' => function (UserCountry $country) use ($locale): string {
                    return $country->getLabel($locale);
                },
                'constraints' => [
                    new NotBlank(message: 'country.not_blank'),
                ],
                'required' => true,
                'placeholder' => $this->translator->trans('register.country_placeholder', [], 'front'),
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue(message: 'terms.not_accepted'),
                ],
                'required' => true,
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
