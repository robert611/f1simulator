<?php

declare(strict_types=1);

namespace Admin\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends BaseController
{
    #[Route('', name: 'app_admin', methods: ['GET'])]
    public function admin(): Response
    {
        return $this->render('@admin/admin.html.twig');
    }
}
