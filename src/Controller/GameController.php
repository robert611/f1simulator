<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\DriverRepository;
use App\Repository\SeasonRepository;
use App\Repository\TeamRepository;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GameSimulation\SimulateRace;
use App\Service\GameSimulation\SimulateQualifications;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Qualification;
use App\Entity\Season;
use App\Entity\Race;
use App\Entity\RaceResult;
use App\Service\DrawDriverToReplace;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly SeasonRepository $seasonRepository,
        private readonly TrackRepository $trackRepository,
        private readonly DriverRepository $driverRepository,
        private readonly DrawDriverToReplace $drawDriverToReplace,
        private readonly SimulateQualifications $simulateQualifications,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/game/season/start', name: 'game_season_start', methods: ['GET', 'POST'])]
    public function startSeason(Request $request): RedirectResponse
    {
        $team = $this->teamRepository->find($request->get('teamId'));

        $driver = $this->drawDriverToReplace->getDriverToReplace($team);

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

    #[Route('/game/simulate/race', name: 'game_simulate_race', methods: ['GET'])]
    public function simulateRace(Session $session): RedirectResponse
    {
        /* First find a season to which race belongs */
        $season = $this->seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);

        if (null === $season) {
            $this->addFlash('error', 'Nie możesz symulować wyścigu, bez rozpoczęcia sezonu.');
            return $this->redirectToRoute('app_index');
        }

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $this->trackRepository->find($lastRace->getTrack()->getId() + 1) : $this->trackRepository->findAll()[0];

        $tracksCount = $this->trackRepository->count();

        if ($season->getRaces()->count() === $tracksCount) {
            $session->getFlashBag()->add('error', 'Wystąpił problem z rozegraniem wyścigu, ze względu bezpieczeństwa danych twój sezon został zakończony.');

            $season->endSeason();

            $this->entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        $race = Race::create($track, $season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        $qualificationsResults = $this->simulateQualifications->getQualificationsResults();
        
        $raceResults = (new SimulateRace)->getRaceResults($this->driverRepository->findAll(), $qualificationsResults);

        /* Save qualifications results in database */
        foreach ($qualificationsResults as $position => $driver) {
            $qualification = new Qualification();

            $qualification->setRace($race);
            $qualification->setDriver($driver);
            $qualification->setPosition($position);

            $this->entityManager->persist($qualification);
        }

        /* Save race results in database */
        foreach ($raceResults as $position => $driverId) {
            $raceResult = new RaceResult();

            $raceResult->setRace($race);
            $raceResult->setDriver($this->driverRepository->find($driverId));
            $raceResult->setPosition($position);

            $this->entityManager->persist($raceResult);
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }
}
