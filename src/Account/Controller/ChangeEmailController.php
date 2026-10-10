<?php

declare(strict_types=1);

namespace Account\Controller;

use Account\Form\ChangeEmail\ChangeEmailType;
use Account\Form\ChangeEmail\ChangeEmailTypeDTO;
use Account\Repository\ChangeEmailConfirmationTokenRepository;
use Account\Service\ChangeEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account/change-email')]
class ChangeEmailController extends BaseController
{
    public function __construct(
        private readonly ChangeEmailService $changeEmailService,
        private readonly ChangeEmailConfirmationTokenRepository $tokenRepository,
        private readonly TranslatorInterface $translator,
        private readonly EntityManagerInterface $entityManager,
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

            $this->changeEmailService->notifyUserInboxToConfirmChangingEmail(
                formData: $formData,
                user: $this->getUser(),
                locale: $request->getLocale(),
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

    #[Route('/confirmation/{token}', name: 'account_email_confirmation', methods: ['GET', 'POST'])]
    public function confirm(string $token): Response
    {
        $changeEmailConfirmationToken = $this->tokenRepository->findOneBy(['token' => $token]);

        if (null === $changeEmailConfirmationToken || false === $changeEmailConfirmationToken->isValid()) {
            if (isset($changeEmailConfirmationToken)) {
                $changeEmailConfirmationToken->invalidate();
                $this->entityManager->flush();
            }

            $this->addFlash(
                'warning',
                $this->translator->trans('account.email.not_existent_confirmation_link', [], 'front'),
            );

            return $this->redirectToRoute('app_index');
        }

        $changeEmailConfirmationToken->invalidate();
        $user = $changeEmailConfirmationToken->getUser();
        $user->setEmail($changeEmailConfirmationToken->getNewEmail());

        $this->entityManager->flush();

        $this->tokenRepository->invalidateUserTokens($user->getId());

        $this->addFlash(
            'success',
            $this->translator->trans('account.email.change_confirmed', [], 'front'),
        );

        return $this->redirectToRoute('app_index');
    }
}
