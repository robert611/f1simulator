<?php

namespace Domain\Controller;

use App\Controller\BaseController;
use Domain\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends BaseController
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
