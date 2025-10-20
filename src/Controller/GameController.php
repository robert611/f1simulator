<?php

declare(strict_types=1);

namespace App\Controller;

use Domain\Repository\TeamRepository;
use Domain\Repository\TrackRepository;
use App\Entity\Season;
use App\Repository\SeasonRepository;
use App\Service\GameSimulation\SimulateRaceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly SimulateRaceService $simulateRaceService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/game/season/start', name: 'game_season_start', methods: ['GET', 'POST'])]
    public function startSeason(Request $request): RedirectResponse
    {
        $team = $this->teamRepository->find($request->get('teamId'));

        $driver = $team->drawDriverToReplace();

        if (null === $driver) {
            $this->addFlash('error', 'Ten zespół nie posiada kierowców. Wybierz inny zespół.');
            return $this->redirectToRoute('app_index');
        }

        $season = Season::create(
            $this->getUser(),
            $driver,
        );

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/game/season/end', name: 'game_season_end', methods: ['GET', 'POST'])]
    public function endSeason(): RedirectResponse
    {
        $season = $this->seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => false]);

        if (null === $season) {
            $this->addFlash('error', 'Nie możesz zakończyć sezonu, bez jego rozpoczęcia.');
            return $this->redirectToRoute('app_index');
        }

        $tracksCount = $this->trackRepository->count();

        if ($season->getRaces()->count() === $tracksCount) {
            $season->endSeason();
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/game/simulate/race', name: 'game_simulate_race', methods: ['GET', 'POST'])]
    public function simulateRace(Session $session): RedirectResponse
    {
        /* First find a season to which race belongs */
        $season = $this->seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => false]);

        if (null === $season) {
            $this->addFlash('error', 'Nie możesz symulować wyścigu, bez rozpoczęcia sezonu.');
            return $this->redirectToRoute('app_index');
        }

        $tracksCount = $this->trackRepository->count();

        if ($season->getRaces()->count() === $tracksCount) {
            /* phpcs:ignore */
            $session->getFlashBag()->add('error', 'Wystąpił problem z rozegraniem wyścigu, ze względu bezpieczeństwa danych twój sezon został zakończony.');

            $season->endSeason();

            $this->entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        $this->simulateRaceService->simulateRace($season);

        return $this->redirectToRoute('app_index');
    }
}
