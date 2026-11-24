<?php

declare(strict_types=1);

namespace OutgoingApi\Controller;

use Computer\ComputerFacadeInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OutgoingApi\Documentation\FormatNotAcceptableDocumentation;
use OutgoingApi\Documentation\SeasonDocumentation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

final class SeasonApiController extends BaseOutgoingApiController
{
    public function __construct(
        private readonly ComputerFacadeInterface $computerFacade,
    ) {
    }

    #[OA\Tag(name: 'Seasons')]
    #[OA\Response(
        response: 200,
        description: 'Returns the list of played offline seasons.',
        content: [
            new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: SeasonDocumentation::class),
                    ),
                ),
            ),
            new OA\MediaType(
                mediaType: 'application/xml',
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: SeasonDocumentation::class),
                    ),
                    xml: new OA\Xml(name: 'seasons', wrapped: true),
                ),
            ),
        ],
    )]
    #[OA\Response(
        response: 401,
        description: 'Authentication failed.',
    )]
    #[OA\Response(
        response: 406,
        description: 'None of the supported formats is acceptable.',
        content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: new Model(type: FormatNotAcceptableDocumentation::class))
        ),
    )]
    #[Route('/api/seasons', name: 'api_seasons_list', methods: ['GET'])]
    public function list(): Response
    {
        $seasons = $this->computerFacade->getUserSeasons($this->getUser()->getId());

        return $this->apiResponse($seasons, xmlRootName: 'seasons', xmlItemName: 'season');
    }

    #[OA\Tag(name: 'Seasons')]
    #[OA\Response(
        response: 200,
        description: 'Returns offline season.',
        content: [
            new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(ref: new Model(type: SeasonDocumentation::class))
            ),
            new OA\MediaType(
                mediaType: 'application/xml',
                schema: new OA\Schema(ref: new Model(type: SeasonDocumentation::class))
            ),
        ],
    )]
    #[OA\Response(
        response: 401,
        description: 'Authentication failed.',
    )]
    #[OA\Response(
        response: 404,
        description: 'Resource not found.',
    )]
    #[OA\Response(
        response: 406,
        description: 'None of the supported formats is acceptable.',
        content: new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(ref: new Model(type: FormatNotAcceptableDocumentation::class))
        ),
    )]
    #[Route('/api/seasons/{id}', name: 'api_seasons_get', methods: ['GET'])]
    public function get(int $id): Response
    {
        $season = $this->computerFacade->getSingleUserSeason($id);

        if (null === $season) {
            return $this->resourceNotFound('Season not found');
        }

        if ($season->getUser()->getId() !== $this->getUser()->getId()) {
            return $this->resourceNotFound('Season not found');
        }

        return $this->apiResponse($season, xmlRootName: 'season');
    }
}
