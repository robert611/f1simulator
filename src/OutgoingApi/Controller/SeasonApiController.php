<?php

declare(strict_types=1);

namespace OutgoingApi\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class SeasonApiController extends AbstractController
{
    #[Route('/api/seasons', name: 'api_seasons_list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of played offline seasons.',
    )]
    #[OA\Tag(name: 'seasons')]
    public function list(): JsonResponse
    {
        return new JsonResponse([]);
    }
}
