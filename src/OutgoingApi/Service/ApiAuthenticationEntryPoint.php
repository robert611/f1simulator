<?php

declare(strict_types=1);

namespace OutgoingApi\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Returns a JSON 401 (instead of an HTML error page) when an API request has no bearer token.
 */
final class ApiAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse(
            [
                'title' => 'Authentication required.',
                'status' => Response::HTTP_UNAUTHORIZED,
                'details' => 'Provide an API token in the "Authorization: Bearer" header.',
            ],
            Response::HTTP_UNAUTHORIZED,
            [
                'Content-Type' => 'application/problem+json',
                'WWW-Authenticate' => 'Bearer',
                'Cache-Control' => 'no-store',
            ],
        );
    }
}
