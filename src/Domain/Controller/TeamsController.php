<?php

namespace Domain\Controller;

use Domain\Entity\Team;
use Domain\Repository\TeamRepository;
use Shared\Controller\BaseController;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $repository,
        private readonly AssetMapperInterface $assetMapper,
    ) {
    }

    #[Route('/teams', name: 'teams', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $assetMapper = $this->assetMapper;

        $teams = array_map(function (Team $team) use ($assetMapper) {
            return [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'picture' => $team->getPicture(),
                'pictureUrl' => $assetMapper->getPublicPath('images/cars/'.$team->getPicture()),
            ];
        }, $this->repository->findAll());

        return new JsonResponse($teams);
    }
}
