<?php

declare(strict_types=1);

namespace OutgoingApi\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseOutgoingApiController extends BaseController
{
    public function resourceNotFound(string $title): JsonResponse
    {
        return new JsonResponse(
            [
                'title' => $title,
                'status' => Response::HTTP_NOT_FOUND,
            ],
            Response::HTTP_NOT_FOUND,
            ['Content-Type' => 'application/problem+json']
        );
    }
}
