<?php

declare(strict_types=1);

namespace Account\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account/delete-account')]
final class DeleteAccountController extends BaseController
{
    #[Route('/index', name: 'account_delete_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@account/delete.html.twig');
    }

    #[Route('/index', name: 'account_delete_submit', methods: ['POST', 'DELETE'])]
    public function delete(Request $request): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete-account', $submittedToken)) {
            // TODO DO SOMETHING
        } else {
            throw $this->createAccessDeniedException();
        }

        return $this->redirectToRoute('app_login');
    }
}
