<?php

declare(strict_types=1);

namespace Account\Controller;

use Account\Form\ChangePassword\ChangePasswordType;
use Account\Form\ChangePassword\ChangePasswordTypeDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/index', name: 'account_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('@account/index.html.twig', [
            'locale' => $request->getLocale(),
        ]);
    }

    #[Route('/change-password', name: 'account_change_password', methods: ['GET'])]
    public function password(Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ChangePasswordTypeDTO $changePasswordDTO */
            $changePasswordDTO = $form->getData();

            return $this->redirectToRoute('account_change_password');
        }

        return $this->render('@account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/email', name: 'account_email', methods: ['GET'])]
    public function email(): Response
    {
        return $this->render('@account/in_progress.html.twig', [
            'header' => 'account.menu.email',
        ]);
    }

    #[Route('/username', name: 'account_username', methods: ['GET'])]
    public function username(): Response
    {
        return $this->render('@account/in_progress.html.twig', [
            'header' => 'account.menu.username',
        ]);
    }

    #[Route('/history', name: 'account_history', methods: ['GET'])]
    public function history(): Response
    {
        return $this->render('@account/in_progress.html.twig', [
            'header' => 'account.menu.history',
        ]);
    }

    #[Route('/delete', name: 'account_delete', methods: ['GET'])]
    public function delete(): Response
    {
        return $this->render('@account/delete.html.twig');
    }
}
