<?php

declare(strict_types=1);

namespace OutgoingApi\Controller;

use Computer\ComputerFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class SeasonApiController extends BaseController
{
    public function __construct(
        private readonly ComputerFacadeInterface $computerFacade,
    ) {
    }

    #[Route('/api/seasons', name: 'api_seasons_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of played offline seasons.',
    )]
    #[OA\Tag(name: 'seasons')]
    public function list(): JsonResponse
    {
        $seasons = $this->computerFacade->getUserSeasons($this->getUser()->getId());

        return new JsonResponse($seasons);
    }
}
