<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Team;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/teams', name: 'teams', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        return new JsonResponse($teams);
    }
}
