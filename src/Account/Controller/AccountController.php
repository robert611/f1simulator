<?php

declare(strict_types=1);

namespace Account\Controller;

use Account\Form\ChangePassword\ChangePasswordType;
use Account\Form\ChangePassword\ChangePasswordTypeDTO;
use Account\Service\ChangePasswordService;
use Computer\ComputerFacadeInterface;
use Security\Entity\User;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account')]
class AccountController extends BaseController
{
    public function __construct(
        private readonly ChangePasswordService $changePasswordService,
        private readonly TranslatorInterface $translator,
        private readonly ComputerFacadeInterface $computerFacade,
    ) {
    }

    #[Route('/index', name: 'account_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('@account/index.html.twig', [
            'locale' => $request->getLocale(),
        ]);
    }

    #[Route('/change-password', name: 'account_change_password', methods: ['GET', 'POST'])]
    public function password(Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangePasswordTypeDTO $changePasswordDTO */
            $changePasswordDTO = $form->getData();

            /** @var User $user */
            $user = $this->getUser();

            $this->changePasswordService->changePassword($user, $changePasswordDTO->newPassword);

            $this->addFlash(
                'success',
                $this->translator->trans('account.password.password_has_been_changed', [], 'front'),
            );

            return $this->redirectToRoute('account_change_password');
        }

        return $this->render('@account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/history', name: 'account_seasons_history', methods: ['GET'])]
    public function history(): Response
    {
        $userSeasons = $this->computerFacade->getUserSeasons($this->getUser()->getId());

        return $this->render('@account/seasons_history.html.twig', [
            'userSeasons' => $userSeasons,
        ]);
    }

    #[Route('/delete', name: 'account_delete', methods: ['GET'])]
    public function delete(): Response
    {
        return $this->render('@account/delete.html.twig');
    }
}
