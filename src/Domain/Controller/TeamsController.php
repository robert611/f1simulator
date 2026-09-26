<?php

declare(strict_types=1);

namespace Domain\Controller;

use Domain\Entity\Driver;
use Domain\Entity\Team;
use Domain\Repository\DriverRepository;
use Domain\Repository\TeamRepository;
use Shared\Controller\BaseController;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TeamsController extends BaseController
{
    public function __construct(
        private readonly TeamRepository $teamRepository,
        private readonly DriverRepository $driverRepository,
        private readonly AssetMapperInterface $assetMapper,
    ) {
    }

    #[Route('/teams', name: 'teams', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $assetMapper = $this->assetMapper;

        $drivers = $this->driverRepository->findAll();

        $teams = array_map(function (Team $team) use ($assetMapper, $drivers) {
            $teamDrivers = Driver::getDriversByTeamId($drivers, $team->getId());

            return [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'picture' => $team->getPicture(),
                'pictureUrl' => $assetMapper->getPublicPath('images/cars/' . $team->getPicture()),
                'drivers' => Driver::getDriversWithoutDependencies($teamDrivers),
            ];
        }, $this->teamRepository->findAll());

        return new JsonResponse($teams);
    }
}
