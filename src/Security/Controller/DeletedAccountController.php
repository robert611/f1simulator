<?php

namespace Security\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DeletedAccountController extends BaseController
{
    #[Route('/security/account-deleted', name: 'app_account_deleted', methods: ['GET'])]
    public function deleted(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render('@security/security/account_deleted.html.twig');
    }
}
