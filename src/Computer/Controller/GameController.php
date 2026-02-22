<?php

declare(strict_types=1);

namespace Computer\Controller;

use Computer\Entity\Season;
use Computer\Repository\SeasonRepository;
use Computer\Service\GameSimulation\SimulateRaceService;
use Doctrine\ORM\EntityManagerInterface;
use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends BaseController
{
    public function __construct(
        private readonly SeasonRepository $seasonRepository,
        private readonly SimulateRaceService $simulateRaceService,
        private readonly EntityManagerInterface $entityManager,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    #[Route('/game/season/start', name: 'game_season_start', methods: ['GET', 'POST'])]
    public function startSeason(Request $request): RedirectResponse
    {
        $team = $this->domainFacade->getTeamById((int) $request->request->get('teamId'));

        $driver = $team->drawDriverToReplace();

        if (null === $driver) {
            $this->addFlash('error', 'Ten zespół nie posiada kierowców. Wybierz inny zespół.');
            return $this->redirectToRoute('app_index');
        }

        $season = Season::create($this->getUser(), $driver->getId());

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

        $tracksCount = $this->domainFacade->getTracksCount();

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

        $tracksCount = $this->domainFacade->getTracksCount();

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
