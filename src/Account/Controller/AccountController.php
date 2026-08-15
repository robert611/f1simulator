<?php

declare(strict_types=1);

namespace Account\Controller;

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

    #[Route('/password', name: 'account_password', methods: ['GET'])]
    public function password(): Response
    {
        return $this->render('@account/password.html.twig');
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
