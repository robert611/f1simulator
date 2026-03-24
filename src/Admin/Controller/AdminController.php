<?php

declare(strict_types=1);

namespace Admin\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends BaseController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function admin(): Response
    {
        return $this->render('@admin/dashboard.html.twig');
    }

    #[Route('/user-country-map', name: 'admin_user_country_map', methods: ['GET'])]
    public function userCountryMap(): Response
    {
        return $this->render('@admin/dashboard/user_country_map.html.twig');
    }
}
