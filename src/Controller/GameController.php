<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Model\GameSimulation\SimulateRace;
use App\Model\GameSimulation\SimulateQualifications;
use Symfony\Component\HttpFoundation\Session\Session;
USE App\Entity\Qualification;
use App\Entity\Season;
use App\Entity\Driver;
use App\Entity\Team;
use App\Entity\Race;
use App\Entity\Track;
use App\Entity\RaceResults;
use App\Model\DrawDriverToReplace;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/game/season/start', name: 'game_season_start', methods: ['GET'])]
    public function startSeason(Request $request): RedirectResponse
    {
        $team = $this->entityManager->getRepository(Team::class)->find($request->get('team'));

        $season = new Season();

        $season->setUser($this->getUser());
        $season->setDriver((new DrawDriverToReplace)->getDriverToReplace($team));
        $season->setCompleted(0);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }

    #[Route('/game/season/end', name: 'game_season_end', methods: ['GET'])]
    public function endSeason(): RedirectResponse
    {
        $seasonRepository = $this->entityManager->getRepository(Season::class);
        $season = $seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);
        
        if (null === $season) {
            $this->addFlash('error', 'Nie możesz zakończyć sezonu, bez jego rozpoczęcia.');
            return $this->redirectToRoute('app_index');
        }

        /* If number of finished races is equal to number of all tracks than season should be end */
        if (count($season->getRaces()) == count($this->entityManager->getRepository(Track::class)->findAll())) {
            $season->setCompleted(1);
            $this->entityManager->persist($season);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/game/simulate/race', name: 'game_simulate_race', methods: ['GET'])]
    public function simulateRace(Session $session): RedirectResponse
    {
        $seasonRepository = $this->entityManager->getRepository(Season::class);
        $trackRepository = $this->entityManager->getRepository(Track::class);
        $driverRepository = $this->entityManager->getRepository(Driver::class);

        /* First find a season to which race belongs */
        $season = $seasonRepository->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);

        if (!$season) {
            $this->addFlash('error', 'Nie możesz symulować wyścigu, bez rozpoczęcia sezonu.');
            return $this->redirectToRoute('app_index');
        }

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $trackRepository->find($lastRace->getTrack()->getId() + 1) : $trackRepository->findAll()[0];

        if (count($season->getRaces()) == count($trackRepository->findAll())) {
            $session->getFlashBag()->add('error', 'Wystąpił problem z rozegraniem wyścigu, ze względu bezpieczeństwa danych twój sezon został zakończony.');

            $season->setCompleted(1);

            $this->entityManager->persist($season);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_index');
        }

        /* Save race in the database */
        $race = new Race();

        $race->setTrack($track);
        $race->setSeason($season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        $qualificationsResults = (new SimulateQualifications)->getQualificationsResults($driverRepository->findAll());
        
        $raceResults = (new SimulateRace)->getRaceResults($driverRepository->findAll(), $qualificationsResults);

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
            $raceResult = new RaceResults();

            $raceResult->setRace($race);
            $raceResult->setDriver($driverRepository->find($driverId));
            $raceResult->setPosition($position);

            $this->entityManager->persist($raceResult);
        }
        
        $this->entityManager->flush();

        return $this->redirectToRoute('app_index');
    }
}
