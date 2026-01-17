<?php

declare(strict_types=1);

namespace Multiplayer\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Domain\Contract\DTO\DriverDTO;
use Domain\Contract\DTO\TrackDTO;
use Domain\DomainFacadeInterface;
use Multiplayer\Entity\UserSeason;
use Multiplayer\Entity\UserSeasonPlayer;
use Multiplayer\Form\UserSeasonFormDTO;
use Multiplayer\Form\UserSeasonType;
use Multiplayer\Repository\UserSeasonRepository;
use Multiplayer\Security\LeagueVoter;
use Multiplayer\Service\ClassificationType;
use Multiplayer\Service\DrawDriverToReplace;
use Multiplayer\Service\GameSimulation\SimulateLeagueRace;
use Multiplayer\Service\LeagueClassifications;
use Multiplayer\Service\LeagueTeamsClassification;
use Multiplayer\Service\SecretGenerator;
use Shared\Controller\BaseController;
use Shared\HashTable;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/player-league')]
class UserSeasonController extends BaseController
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly DrawDriverToReplace $drawDriverToReplace,
        private readonly SimulateLeagueRace $simulateLeagueRace,
        private readonly SecretGenerator $secretGenerator,
        private readonly DomainFacadeInterface $domainFacade,
        private readonly LeagueTeamsClassification $leagueTeamsClassification,
        private readonly LeagueClassifications $leagueClassification,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/{id}/show/{classificationType}', name: 'user_season_show', methods: ['GET'])]
    public function show(
        Request $request,
        UserSeason $season,
        ClassificationType $classificationType = ClassificationType::PLAYERS,
    ): Response {
        $this->denyAccessUnlessGranted(LeagueVoter::SHOW_SEASON, $season);

        $player = $this->entityManager->getRepository(UserSeasonPlayer::class)->findOneBy([
            'season' => $season,
            'user' => $this->getUser(),
        ]);

        $numberOfRacesInSeason = $this->domainFacade->getTracksCount();
        $lastTrack = null;

        if ($season->getRaces()->count()) {
            $track = $this->domainFacade->getNextTrack($season->getRaces()->last()->getTrackId());
            $lastTrack = $this->domainFacade->getTrackById($season->getRaces()->last()->getTrackId());
        } else {
            $track = $this->domainFacade->getFirstTrack();
        }

        $tracks = $this->domainFacade->getAllTracks();
        /** @var TrackDTO[] $tracks */
        $tracks = HashTable::fromObjectArray($tracks, 'getId');

        $drivers = $this->domainFacade->getAllDrivers();
        /** @var DriverDTO[] $drivers */
        $drivers = HashTable::fromObjectArray($drivers, 'getId');

        if ($season->getRaces()->count() === 0) {
            $classificationType = ClassificationType::PLAYERS;
        }

        $raceId = $request->query->get('race_id');

        if (is_numeric($raceId)) {
            $raceId = (int) $raceId;
        }

        $classification = $this->leagueClassification->getClassificationBasedOnType(
            $season,
            $classificationType,
            $raceId,
        );

        $teamsClassification = $this->leagueTeamsClassification->getClassification($season);

        return $this->render('@multiplayer/user_season/show.html.twig', [
            'league' => $season,
            'player' => $player,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'track' => $track,
            'lastTrack' => $lastTrack,
            'tracks' => $tracks,
            'drivers' => $drivers,
            'classificationType' => $classificationType,
            'classification' => $classification,
            'teamsClassification' => $teamsClassification,
        ]);
    }

    #[Route('/create', name: 'user_season_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(UserSeasonType::class, new UserSeasonFormDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->userSeasonRepository->count(['owner' => $this->getUser(), 'completed' => false]) >= 3) {
                $this->addFlash('warning', 'W jednym momencie możesz mieć maksymalnie trzy nieukończone ligi');

                return $this->redirectToRoute('multiplayer_index');
            }

            /** @var UserSeasonFormDTO $userSeasonFormDTO */
            $userSeasonFormDTO = $form->getData();

            $userSeason = UserSeason::create(
                $this->secretGenerator->getLeagueUniqueSecret(),
                $userSeasonFormDTO->maxPlayers,
                $this->getUser(),
                $userSeasonFormDTO->name,
            );

            $driver = $this->drawDriverToReplace->getDriverToReplaceInUserLeague($userSeason);

            $userSeasonPlayer = UserSeasonPlayer::create($userSeason, $this->getUser(), $driver->getId());

            $this->entityManager->persist($userSeason);
            $this->entityManager->persist($userSeasonPlayer);
            $this->entityManager->flush();

            $this->addFlash('success', 'Liga została stworzona');
        }

        return $this->redirectToRoute('multiplayer_index');
    }

    #[Route('/join', name: 'user_season_join', methods: ['GET', 'POST'])]
    public function join(Request $request): RedirectResponse
    {
        $secret = $request->request->get('user_season_secret');
        $league = $this->userSeasonRepository->findOneBy(['secret' => $secret]);

        $this->denyAccessUnlessGranted(LeagueVoter::JOIN, $league);

        $driver = $this->drawDriverToReplace->getDriverToReplaceInUserLeague($league);

        if (null === $driver) {
            $this->addFlash('warning', 'Brakuje kierowców, w których możesz się wcielić.');

            return $this->redirectToRoute('multiplayer_index');
        }

        $player = UserSeasonPlayer::create($league, $this->getUser(), $driver->getId());

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $this->redirectToRoute('multiplayer_index');
    }

    #[Route('/{id}/start', name: 'user_season_start', methods: ['GET'])]
    public function start(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::START, $season);

        $season->start();

        $this->entityManager->flush();

        return $this->redirectToRoute('user_season_show', ['id' => $season->getId()]);
    }

    #[Route('/{id}/end', name: 'user_season_end', methods: ['GET'])]
    public function end(UserSeason $season): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::END, $season);

        $season->end();

        $this->entityManager->flush();

        return $this->redirectToRoute('user_season_show', ['id' => $season->getId()]);
    }

    /**
     * @throws Throwable
     */
    #[Route('/{id}/simulate/race', name: 'user_season_simulate_race', methods: ['GET'])]
    public function simulateRace(UserSeason $userSeason): RedirectResponse
    {
        $this->denyAccessUnlessGranted(LeagueVoter::SIMULATE_RACE, $userSeason);

        $this->simulateLeagueRace->simulateRace($userSeason);

        return $this->redirectToRoute('user_season_show', ['id' => $userSeason->getId()]);
    }
}
