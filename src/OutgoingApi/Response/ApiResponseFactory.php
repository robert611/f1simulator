<?php

declare(strict_types=1);

namespace OutgoingApi\Response;

use OutgoingApi\ContentNegotiation\ApiFormat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final readonly class ApiResponseFactory
{
    /**
     * XML namespace of problem details documents (RFC 9457, appendix B).
     */
    private const string PROBLEM_XML_NAMESPACE = 'urn:ietf:rfc:7807';

    public function __construct(
        private RequestStack $requestStack,
        private XmlEncoder $xmlEncoder,
    ) {
    }

    public function create(
        mixed $data,
        string $xmlRootName,
        string $xmlItemName = 'item',
        int $status = Response::HTTP_OK,
    ): Response {
        $format = $this->negotiatedFormat();

        return match ($format) {
            ApiFormat::Json => new JsonResponse($data, $status),
            ApiFormat::Xml => new Response(
                $this->xmlEncoder->encode($data, $xmlRootName, $xmlItemName),
                $status,
                ['Content-Type' => $format->mimeTypes()[0]],
            ),
        };
    }

    public function problem(string $title, int $status): Response
    {
        $format = $this->negotiatedFormat();
        $data = [
            'title' => $title,
            'status' => $status,
        ];

        return match ($format) {
            ApiFormat::Json => new JsonResponse($data, $status, ['Content-Type' => $format->problemMimeType()]),
            ApiFormat::Xml => new Response(
                $this->xmlEncoder->encode($data, 'problem', namespace: self::PROBLEM_XML_NAMESPACE),
                $status,
                ['Content-Type' => $format->problemMimeType()],
            ),
        };
    }

    private function negotiatedFormat(): ApiFormat
    {
        $requestFormat = $this->requestStack->getCurrentRequest()?->getRequestFormat();

        return ApiFormat::tryFrom((string) $requestFormat) ?? ApiFormat::default();
    }
}
