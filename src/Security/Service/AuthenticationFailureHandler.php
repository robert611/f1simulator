<?php

declare(strict_types=1);

namespace Security\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

final readonly class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof UserNotConfirmedException) {
            return new RedirectResponse(
                $this->router->generate('app_resend_confirm_email_view', ['userId' => $exception->getUserId()]),
            );
        }

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
