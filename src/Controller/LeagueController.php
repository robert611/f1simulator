<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayers;
use App\Entity\UserSeasonRaces;
use App\Entity\UserSeasonQualifications;
use App\Entity\UserSeasonRaceResults;
use App\Entity\Driver;
use App\Entity\Track;
use App\Model\DrawDriverToReplace;
use App\Model\GameSimulation\SimulateLeagueRace;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/join', name: 'league_join', methods: ['GET'])]
    public function join(Request $request): RedirectResponse
    {
        $secret = $request->request->get('league-secret');
        $league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted('league_join', $league);

        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

        $player = new UserSeasonPlayers();
        $player->setUser($this->getUser());
        $player->setDriver((new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers,  $league->getPlayers()));
        $player->setSeason($league);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_index');
    }

    #[Route('/{id}/start', name: 'league_start', methods: ['GET'])]
    public function startLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted('league_start', $season);

        $season->setStarted(true);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/end', name: 'league_end', methods: ['GET'])]
    public function endLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted('league_end', $season);

        $season->setCompleted(1);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/simulate/race', name: 'league_simulate_race', methods: ['GET'])]
    public function simulateRace(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted('league_simulate_race', $season);

        $trackRepository = $this->entityManager->getRepository(Track::class);

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $trackRepository->find($lastRace->getTrack()->getId() + 1) : $trackRepository->findAll()[0];

        /* Save race in the database */
        $race = new UserSeasonRaces();

        $race->setTrack($track);
        $race->setSeason($season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        [$qualificationsResults, $raceResults] = (new SimulateLeagueRace)->getRaceResults($season->getPlayers());

        /* Save qualifications results in database, element index is equivalent to its position */
        foreach ($qualificationsResults as $position => $player) {
            $qualification = new UserSeasonQualifications();
            $qualification->setRace($race);
            $qualification->setPlayer($player);
            $qualification->setPosition($position);

            $this->entityManager->persist($qualification);
            $this->entityManager->flush();
        }
        
        /* Save race results in database */
        foreach ($raceResults as $position => $player) {
            $raceResult = new UserSeasonRaceResults();
            $raceResult->setRace($race);
            $raceResult->setPlayer($player);
            $raceResult->setPosition($position);

            $this->entityManager->persist($raceResult);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }
}
