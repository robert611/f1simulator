<?php

declare(strict_types=1);

namespace Admin\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-driver')]
class AdminDriverController extends BaseController
{
    #[Route('', name: 'admin_driver', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@admin/admin_driver/index.html.twig');
    }
}
