<?php

declare(strict_types=1);

namespace OutgoingApi\Controller;

use OutgoingApi\Response\ApiResponseFactory;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class BaseOutgoingApiController extends BaseController
{
    public static function getSubscribedServices(): array
    {
        return [
            ...parent::getSubscribedServices(),
            ApiResponseFactory::class,
        ];
    }

    protected function apiResponse(
        mixed $data,
        string $xmlRootName,
        string $xmlItemName = 'item',
        int $status = Response::HTTP_OK,
    ): Response {
        return $this->apiResponseFactory()->create($data, $xmlRootName, $xmlItemName, $status);
    }

    public function resourceNotFound(string $title): Response
    {
        return $this->apiResponseFactory()->problem($title, Response::HTTP_NOT_FOUND);
    }

    private function apiResponseFactory(): ApiResponseFactory
    {
        return $this->container->get(ApiResponseFactory::class);
    }
}
