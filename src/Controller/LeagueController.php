<?php

namespace App\Controller;

use App\Repository\UserSeasonRepository;
use App\Security\LeagueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonRace;
use App\Entity\UserSeasonQualification;
use App\Entity\UserSeasonRaceResult;
use App\Entity\Driver;
use App\Entity\Track;
use App\Service\DrawDriverToReplace;
use App\Service\GameSimulation\SimulateLeagueRace;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueController extends BaseController
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly SimulateLeagueRace $simulateLeagueRace,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/join', name: 'league_join', methods: ['GET', 'POST'])]
    public function join(Request $request): RedirectResponse
    {
        $secret = $request->request->get('league-secret');
        $league = $this->userSeasonRepository->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted(LeagueVoter::JOIN, $league);

        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

        $driver = (new DrawDriverToReplace)->getDriverToReplaceInUserLeague($drivers,  $league->getPlayers());

        $player = UserSeasonPlayer::create($league, $this->getUser(), $driver);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_index');
    }

    #[Route('/{id}/start', name: 'league_start', methods: ['GET'])]
    public function startLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::JOIN, $season);

        $season->setStarted(true);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/end', name: 'league_end', methods: ['GET'])]
    public function endLeague(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::END, $season);

        $season->setCompleted(1);

        $this->entityManager->persist($season);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }

    #[Route('/{id}/simulate/race', name: 'league_simulate_race', methods: ['GET'])]
    public function simulateRace(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::SIMULATE_RACE, $season);

        $trackRepository = $this->entityManager->getRepository(Track::class);

        $lastRace = $season->getRaces()->last();
        $track = $lastRace ? $trackRepository->find($lastRace->getTrack()->getId() + 1) : $trackRepository->findAll()[0];

        /* Save race in the database */
        $race = new UserSeasonRace();

        $race->setTrack($track);
        $race->setSeason($season);

        $this->entityManager->persist($race);
        $this->entityManager->flush();

        [$qualificationsResults, $raceResults] = $this->simulateLeagueRace->getRaceResults($season->getPlayers());

        /* Save qualifications results in database, element index is equivalent to its position */
        foreach ($qualificationsResults as $position => $player) {
            $qualification = new UserSeasonQualification();
            $qualification->setRace($race);
            $qualification->setPlayer($player);
            $qualification->setPosition($position);

            $this->entityManager->persist($qualification);
            $this->entityManager->flush();
        }
        
        /* Save race results in database */
        foreach ($raceResults as $position => $player) {
            $raceResult = new UserSeasonRaceResult();
            $raceResult->setRace($race);
            $raceResult->setPlayer($player);
            $raceResult->setPosition($position);

            $this->entityManager->persist($raceResult);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('multiplayer_show_season', ['id' => $season->getId()]);
    }
}
