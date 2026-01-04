<?php

declare(strict_types=1);

namespace Admin\Controller;

use Admin\Form\DriverFormModel;
use Admin\Form\DriverType;
use Domain\Contract\DriverServiceFacadeInterface;
use Domain\Contract\Exception\CarNumberTakenException;
use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-driver')]
class AdminDriverController extends BaseController
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
        private readonly DriverServiceFacadeInterface $driverServiceFacade,
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

    #[Route('/new', name: 'admin_driver_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $driverFormModel = new DriverFormModel();
        $form = $this->createForm(DriverType::class, $driverFormModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DriverFormModel $driverFormModel */
            $driverFormModel = $form->getData();

            try {
                $this->driverServiceFacade->add(
                    $driverFormModel->name,
                    $driverFormModel->surname,
                    $driverFormModel->teamId,
                    $driverFormModel->carNumber,
                );

                $this->addFlash('admin_success', 'Dodano nowego kierowcę');

                return $this->redirectToRoute('admin_driver');
            } catch (CarNumberTakenException) {
                $form->addError(new FormError('Istnieje już kierowca z tym numerem samochodu'));
            }
        }

        return $this->render('@admin/admin_driver/new.html.twig', [
            'driverFormModel' => $driverFormModel,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_driver_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, int $id): Response
    {
        $driver = $this->domainFacade->getDriverById($id);

        $form = $this->createForm(DriverType::class, DriverFormModel::fromDriver($driver));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var DriverFormModel $driverFormModel */
            $driverFormModel = $form->getData();
            $this->driverServiceFacade->update(
                $driver->getId(),
                $driverFormModel->name,
                $driverFormModel->surname,
                $driverFormModel->teamId,
                $driverFormModel->carNumber,
            );

            return $this->redirectToRoute('admin_driver_edit', ['id' => $id]);
        }

        return $this->render('@admin/admin_driver/edit.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }
}
