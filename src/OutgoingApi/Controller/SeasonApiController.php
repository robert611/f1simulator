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
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                properties: [
                    new OA\Property(
                        property: 'id',
                        type: 'integer',
                        example: 1000,
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'driverId',
                        type: 'integer',
                        example: 1000,
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'userId',
                        type: 'integer',
                        example: 1000,
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'position',
                        type: 'integer',
                        example: 8,
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'completed',
                        type: 'boolean',
                        example: false,
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'completedAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2025-07-23T15:43:46Z',
                        nullable: true,
                    ),
                    new OA\Property(
                        property: 'createdAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2025-07-22T10:00:15Z',
                        nullable: false,
                    ),
                    new OA\Property(
                        property: 'updatedAt',
                        type: 'string',
                        format: 'date-time',
                        example: '2025-07-23T17:12:05Z',
                        nullable: false,
                    ),
                ],
                type: 'object',
            ),
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Authentication failed.',
    )]
    #[OA\Tag(name: 'Seasons')]
    public function list(): JsonResponse
    {
        $seasons = $this->computerFacade->getUserSeasons($this->getUser()->getId());

        return new JsonResponse($seasons);
    }
}
