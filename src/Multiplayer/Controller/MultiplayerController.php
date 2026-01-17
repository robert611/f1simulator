<?php

declare(strict_types=1);

namespace Multiplayer\Controller;

use Domain\Contract\DTO\TrackDTO;
use Domain\DomainFacadeInterface;
use Multiplayer\Form\UserSeasonFormDTO;
use Multiplayer\Form\UserSeasonType;
use Multiplayer\Repository\UserSeasonRepository;
use Shared\Controller\BaseController;
use Shared\HashTable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/multiplayer')]
class MultiplayerController extends BaseController
{
    public function __construct(
        private readonly UserSeasonRepository $userSeasonRepository,
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    #[Route('', name: 'multiplayer_index', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $form = $this->createForm(UserSeasonType::class, new UserSeasonFormDTO(), [
            'action' => $this->generateUrl('user_season_create'),
            'method' => 'POST',
        ]);

        $userLeagues = $this->userSeasonRepository->findBy(['owner' => $this->getUser()]);
        $leagues = $this->userSeasonRepository->getUserSeasons($this->getUser()->getId());
        $tracks = $this->domainFacade->getAllTracks();
        /** @var TrackDTO[] $tracks */
        $tracks = HashTable::fromObjectArray($tracks, 'getId');

        return $this->render('@multiplayer/index.html.twig', [
            'form' => $form->createView(),
            'userLeagues' => $userLeagues,
            'leagues' => $leagues,
            'tracks' => $tracks,
        ]);
    }
}
