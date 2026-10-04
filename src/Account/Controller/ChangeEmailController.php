<?php

declare(strict_types=1);

namespace Account\Controller;

use Account\Form\ChangeEmail\ChangeEmailType;
use Account\Form\ChangeEmail\ChangeEmailTypeDTO;
use Mailer\Contract\GenericEmail;
use Mailer\MailerFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account/change-email')]
class ChangeEmailController extends BaseController
{
    public function __construct(
        private readonly MailerFacadeInterface $mailerFacade,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('', name: 'account_email', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ChangeEmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangeEmailTypeDTO $formData */
            $formData = $form->getData();

            if ('pl' === $request->getLocale()) {
                $htmlTemplate = '@mailer/change_email/change_email_pl.html.twig';
                $plainTemplate = '@mailer/change_email/change_email_pl.txt.twig';
            } else {
                $htmlTemplate = '@mailer/change_email/change_email_en.html.twig';
                $plainTemplate = '@mailer/change_email/change_email_en.txt.twig';
            }

            $this->mailerFacade->send(
                new GenericEmail(
                    to: [$this->getUser()->getEmail()],
                    subject: $this->translator->trans('account.email.confirmation_mail_subject', [], 'front'),
                    htmlTemplate: $htmlTemplate,
                    plainTemplate: $plainTemplate,
                    contentParams: [
                        'username' => $this->getUser()->getUsername(),
                        'newEmail' => $formData->newEmail,
                        'changeEmailUrl' => '',
                    ],
                ),
            );

            $this->addFlash(
                'success',
                $this->translator->trans('account.email.confirmation_mail_sent', [], 'front'),
            );

            return $this->redirectToRoute('account_email');
        }

        return $this->render('@account/change_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
