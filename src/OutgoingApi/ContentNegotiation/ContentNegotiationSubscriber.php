<?php

declare(strict_types=1);

namespace OutgoingApi\ContentNegotiation;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ContentNegotiationSubscriber implements EventSubscriberInterface
{
    public const string ROUTE_ATTRIBUTE = '_outgoing_api';

    public function __construct(
        private ContentNegotiator $contentNegotiator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // After the Firewall (8), so the API token is always verified before the format is negotiated.
            KernelEvents::REQUEST => ['onKernelRequest', 7],
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMainRequest() || !$this->isOutgoingApiRequest($request)) {
            return;
        }

        $format = $this->contentNegotiator->negotiate($request->headers->get('Accept'));

        if (null === $format) {
            $event->setResponse($this->notAcceptable());

            return;
        }

        $request->setRequestFormat($format->value);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->isOutgoingApiRequest($event->getRequest())) {
            return;
        }

        // The response body depends on the "Accept" header, so caches must not mix representations
        $event->getResponse()->setVary('Accept', false);
    }

    private function isOutgoingApiRequest(Request $request): bool
    {
        return $request->attributes->getBoolean(self::ROUTE_ATTRIBUTE);
    }

    private function notAcceptable(): JsonResponse
    {
        $supportedMimeTypes = array_merge(...array_map(
            static fn (ApiFormat $format): array => $format->mimeTypes(),
            ApiFormat::cases(),
        ));

        return new JsonResponse(
            [
                'title' => 'Not Acceptable',
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'detail' => 'Supported media types: ' . implode(', ', $supportedMimeTypes) . '.',
            ],
            Response::HTTP_NOT_ACCEPTABLE,
            ['Content-Type' => 'application/problem+json'],
        );
    }
}
