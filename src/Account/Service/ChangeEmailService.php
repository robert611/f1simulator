<?php

declare(strict_types=1);

namespace Account\Service;

use Account\Entity\ChangeEmailConfirmationToken;
use Account\Form\ChangeEmail\ChangeEmailTypeDTO;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Security\Entity\User;
use Shared\Service\TokenGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ChangeEmailService
{
    public function __construct(
        private MailerFacadeInterface $mailerFacade,
        private TranslatorInterface $translator,
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
    ) {
    }

    public function notifyUserInboxToConfirmChangingEmail(
        ChangeEmailTypeDTO $formData,
        User $user,
        string $locale,
    ): void {
        if ('pl' === $locale) {
            $htmlTemplate = '@mailer/change_email/change_email_pl.html.twig';
            $plainTemplate = '@mailer/change_email/change_email_pl.txt.twig';
        } else {
            $htmlTemplate = '@mailer/change_email/change_email_en.html.twig';
            $plainTemplate = '@mailer/change_email/change_email_en.txt.twig';
        }

        $token = TokenGenerator::bin2hex(24);

        $this->mailerFacade->send(
            new GenericEmail(
                to: [$user->getEmail()],
                subject: $this->translator->trans('account.email.confirmation_mail_subject', [], 'front'),
                htmlTemplate: $htmlTemplate,
                plainTemplate: $plainTemplate,
                contentParams: [
                    'username' => $user->getUsername(),
                    'newEmail' => $formData->newEmail,
                    'changeEmailUrl' => $this->router->generate(
                        'account_email_confirmation',
                        ['token' => $token],
                        UrlGeneratorInterface::ABSOLUTE_URL,
                    ),
                ],
            ),
        );

        $changeEmailConfirmationToken = ChangeEmailConfirmationToken::create(
            user: $user,
            newEmail: $formData->newEmail,
            token: $token,
            expiryAt: new DateTimeImmutable('+1 hour'),
        );

        $this->entityManager->persist($changeEmailConfirmationToken);
        $this->entityManager->flush();
    }
}
