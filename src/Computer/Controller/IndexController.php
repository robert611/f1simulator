<?php

declare(strict_types=1);

namespace Computer\Controller;

use Computer\Service\ClassificationType;
use Computer\Service\CurrentDriverSeasonService;
use Computer\Service\SeasonClassifications;
use Computer\Service\SeasonTeamsClassification;
use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TrackDTO;
use Domain\DomainFacadeInterface;
use Security\Event\UserRegisteredEvent;
use Shared\Controller\BaseController;
use Shared\HashTable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class IndexController extends BaseController
{
    public function __construct(
        private readonly CurrentDriverSeasonService $currentDriverSeasonService,
        private readonly SeasonClassifications $seasonClassifications,
        private readonly SeasonTeamsClassification $seasonTeamsClassification,
        private readonly DomainFacadeInterface $domainFacade,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route('/home/{classificationType}', name: 'app_index', methods: ['GET'])]
    public function index(
        Request $request,
        ClassificationType $classificationType = ClassificationType::DRIVERS,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $raceId = $request->query->get('raceId');

        if (is_numeric($raceId)) {
            $raceId = (int) $raceId;
        }

        $currentDriverSeason = $this->currentDriverSeasonService->buildCurrentDriverSeasonData(
            $this->getUser()->getId(),
            $classificationType,
            $raceId,
        );

        if (null === $currentDriverSeason) {
            $classificationType = ClassificationType::DRIVERS;
        }

        $defaultDriversClassification = $this->seasonClassifications->getDefaultDriversClassification();

        $defaultTeamsClassification = $this->seasonTeamsClassification->getDefaultTeamsClassification();

        $tracks = $this->domainFacade->getAllTracks();
        /** @var TrackDTO[] $tracks */
        $tracks = HashTable::fromObjectArray($tracks, 'getId');

        $drivers = $this->domainFacade->getAllDrivers();
        /** @var DriverDTO[] $drivers */
        $drivers = HashTable::fromObjectArray($drivers, 'getId');

        $this->eventDispatcher->dispatch(new UserRegisteredEvent($this->getUser()));

        return $this->render('@computer/index.html.twig', [
            'defaultDriversClassification' => $defaultDriversClassification,
            'classificationType' => $classificationType,
            'defaultTeamsClassification' => $defaultTeamsClassification,
            'currentDriverSeason' => $currentDriverSeason,
            'tracks' => $tracks,
            'drivers' => $drivers,
        ]);
    }

    #[Route('/', name: 'app_redirect_to_route', methods: ['GET'])]
    public function redirectToHome(): RedirectResponse
    {
        return $this->redirectToRoute('app_index');
    }
}
