<?php

declare(strict_types=1);

namespace Security\Service;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

#[AsDecorator('security.authentication.failure_handler.main.form_login')]
final readonly class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    public function __construct(
        #[AutowireDecorated] private AuthenticationFailureHandlerInterface $inner,
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

        if ($exception instanceof UserIsBlockedException) {
            return new RedirectResponse(
                $this->router->generate('app_account_blocked'),
            );
        }

        return $this->inner->onAuthenticationFailure($request, $exception);
    }
}
