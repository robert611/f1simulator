<?php

declare(strict_types=1);

namespace Admin\Controller;

use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-driver')]
class AdminDriverController extends BaseController
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    #[Route('', name: 'admin_driver', methods: ['GET'])]
    public function index(): Response
    {
        $drivers = $this->domainFacade->getAllDrivers();

        return $this->render('@admin/admin_driver/index.html.twig', [
            'drivers' => $drivers,
        ]);
    }
}
