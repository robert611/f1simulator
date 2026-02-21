<?php

declare(strict_types=1);

namespace Security\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Security\Event\UserConfirmedEvent;
use Security\Repository\UserConfirmationTokenRepository;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ConfirmationController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserConfirmationTokenRepository $userConfirmationTokenRepository,
        private readonly TranslatorInterface $translator,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/confirm-email/{token}', name: 'app_confirm_email', methods: ['GET'])]
    public function confirmEmail(string $token): RedirectResponse
    {
        $userConfirmationToken = $this->userConfirmationTokenRepository->findOneBy(['token' => $token]);

        if (null === $userConfirmationToken || false === $userConfirmationToken->isValidAndNotExpired()) {
            if (isset($userConfirmationToken)) {
                $userConfirmationToken->invalidate();
                $this->entityManager->flush();
            }

            $this->addFlash('warning', $this->translator->trans('not_existent_confirmation_link', [], 'security'));

            return $this->redirectToRoute('app_login');
        }

        $user = $userConfirmationToken->getUser();
        $userConfirmationToken->invalidate();
        $user->confirm();
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new UserConfirmedEvent($user));

        $this->addFlash('success', $this->translator->trans('account_confirmed', [], 'security'));

        return $this->redirectToRoute('app_login');
    }

    #[Route('/resend-confirmation-email/{userId}', name: 'app_resend_confirm_email', methods: ['GET'])]
    public function resendEmail(int $userId): Response
    {
        return $this->render('@security/registration/resend_confirmation_email.html.twig');
    }
}
