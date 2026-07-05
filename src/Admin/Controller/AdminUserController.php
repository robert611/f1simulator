<?php

declare(strict_types=1);

namespace Admin\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-user')]
class AdminUserController extends BaseController
{
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@admin/admin_user/index.html.twig');
    }
}
