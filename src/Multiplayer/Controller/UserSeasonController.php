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
use Multiplayer\Service\DrawDriverToReplace;
use Multiplayer\Security\LeagueVoter;
use Multiplayer\Service\ClassificationType;
use Multiplayer\Service\LeagueClassifications;
use Multiplayer\Service\LeagueTeamsClassification;
use Multiplayer\Service\SecretGenerator;
use Shared\Controller\BaseController;
use Shared\HashTable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multiplayer')]
class UserSeasonController extends BaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LeagueTeamsClassification $leagueTeamsClassification,
        private readonly LeagueClassifications $leagueClassification,
        private readonly DrawDriverToReplace $drawDriverToReplace,
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly SecretGenerator $secretGenerator,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    #[Route('', name: 'multiplayer_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $form = $this->createForm(UserSeasonType::class, new UserSeasonFormDTO(), [
            'action' => $this->generateUrl('multiplayer_create'),
            'method' => 'POST',
        ]);

        $userLeagues = $this->userSeasonRepository->findBy(['owner' => $this->getUser()]);
        $leagues = $this->userSeasonRepository->getUserSeasons($this->getUser()->getId());
        $tracks = $this->domainFacade->getAllTracks();
        /** @var TrackDTO[] $tracks */
        $tracks = HashTable::fromObjectArray($tracks, 'getId');

        return $this->render('@multiplayer/league/index.html.twig', [
            'form' => $form->createView(),
            'userLeagues' => $userLeagues,
            'leagues' => $leagues,
            'tracks' => $tracks,
        ]);
    }

    #[Route('/create', name: 'multiplayer_create', methods: ['POST'])]
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

    #[Route('/{id}/show/{classificationType}', name: 'multiplayer_show_season', methods: ['GET'])]
    public function showSeason(
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

        if ($season->getRaces()->count()) {
            $track = $this->domainFacade->getNextTrack($season->getRaces()->last()->getTrackId());
        } else {
            $track = $this->domainFacade->getFirstTrack();
        }

        $lastTrack = $this->domainFacade->getTrackById($season->getRaces()->last()->getTrackId());

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

        return $this->render('@multiplayer/league/show_league.html.twig', [
            'league' => $season,
            'player' => $player,
            'numberOfRacesInSeason' => $numberOfRacesInSeason,
            'track' => $track,
            'lastTrack' => $lastTrack,
            'tracks' => $tracks,
            'drivers' => $drivers,
            'classificationType' => $classificationType,
            'classification' => $classification,
            'teamsClassification' => $teamsClassification
        ]);
    }
}
