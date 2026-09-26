<?php

namespace Security\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlockedAccountController extends BaseController
{
    #[Route('/security/account-blocked', name: 'app_account_blocked', methods: ['GET'])]
    public function blocked(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('@security/security/account_blocked.html.twig');
    }
}
