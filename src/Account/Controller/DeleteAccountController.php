<?php

declare(strict_types=1);

namespace Account\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account/delete-account')]
final class DeleteAccountController extends BaseController
{
    #[Route('/index', name: 'account_delete_index', methods: ['GET'])]
    public function delete(): Response
    {
        return $this->render('@account/delete.html.twig');
    }
}
