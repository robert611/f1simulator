<?php

declare(strict_types=1);

namespace Account\Service;

use Account\Form\ChangeEmail\ChangeEmailTypeDTO;
use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Security\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class ChangeEmailService
{
    public function __construct(
        private MailerFacadeInterface $mailerFacade,
        private TranslatorInterface $translator,
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

        $this->mailerFacade->send(
            new GenericEmail(
                to: [$user->getEmail()],
                subject: $this->translator->trans('account.email.confirmation_mail_subject', [], 'front'),
                htmlTemplate: $htmlTemplate,
                plainTemplate: $plainTemplate,
                contentParams: [
                    'username' => $user->getUsername(),
                    'newEmail' => $formData->newEmail,
                    'changeEmailUrl' => '',
                ],
            ),
        );
    }
}
