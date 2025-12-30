<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Form\DriverFormModel;
use Admin\Form\DriverType;
use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/{id}/edit', name: 'admin_driver_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, int $id): Response
    {
        $driver = $this->domainFacade->getDriverById($id);

        $form = $this->createForm(DriverType::class, DriverFormModel::fromDriver($driver));
        $form->handleRequest($request);

        return $this->render('@admin/admin_driver/edit.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }
}
