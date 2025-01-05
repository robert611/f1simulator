<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Form\DriverType;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/driver')]
class DriverController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'driver_index', methods: ['GET'])]
    public function index(DriverRepository $driverRepository): Response
    {
        return $this->render('driver/index.html.twig', [
            'drivers' => $driverRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'driver_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $driver = new Driver();
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($driver);
            $this->entityManager->flush();

            return $this->redirectToRoute('driver_index');
        }

        return $this->render('driver/new.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'driver_show', methods: ['GET'])]
    public function show(Driver $driver): Response
    {
        return $this->render('driver/show.html.twig', [
            'driver' => $driver,
        ]);
    }

    #[Route('/{id}/edit', name: 'driver_edit', methods: ["GET","POST"])]
    public function edit(Request $request, Driver $driver): Response
    {
        $form = $this->createForm(DriverType::class, $driver);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('driver_index');
        }

        return $this->render('driver/edit.html.twig', [
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'driver_delete', methods: ["DELETE"])]
    public function delete(Request $request, Driver $driver): Response
    {
        if ($this->isCsrfTokenValid('delete'.$driver->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($driver);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('driver_index');
    }
}
