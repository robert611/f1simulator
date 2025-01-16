<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Classification\ClassificationType;
use App\Service\CurrentDriverSeasonService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Race;
use App\Service\Classification\SeasonClassifications;
use App\Service\Classification\SeasonTeamsClassification;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CurrentDriverSeasonService $currentDriverSeasonService,
        private readonly SeasonClassifications $seasonClassifications,
        private readonly SeasonTeamsClassification $seasonTeamsClassification,
    ) {
    }

    #[Route('/home/{classificationType}', name: 'app_index', methods: ['GET'])]
    public function index(
        Request $request,
        ClassificationType $classificationType = ClassificationType::DRIVERS,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $currentDriverSeason = $this->currentDriverSeasonService->buildCurrentDriverSeasonData(
            $this->getUser()->getId(),
            $classificationType,
            $request->query->get('race_id'),
        );

        if (null === $currentDriverSeason) {
            $classificationType = ClassificationType::DRIVERS;
        }

        $defaultDriversClassification = $this->seasonClassifications->getDefaultDriversClassification();

        $defaultTeamsClassification = $this->seasonTeamsClassification->getDefaultTeamsClassification();

        $raceName = $request->query->has('race_id') ? $this->entityManager->getRepository(Race::class)->find($request->query->get('race_id'))->getTrack()->getName() : null;

        return $this->render('index.html.twig', [
            'raceName' => $raceName,
            'defaultDriversClassification' => $defaultDriversClassification,
            'classificationType' => $classificationType,
            'defaultTeamsClassification' => $defaultTeamsClassification,
            'currentDriverSeason' => $currentDriverSeason,
        ]);
    }

    #[Route('/', name: 'app_redirect_to_route', methods: ['GET'])]
    public function redirectToHome(): RedirectResponse
    {
        return $this->redirectToRoute('app_index');
    }
}
