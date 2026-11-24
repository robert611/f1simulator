<?php

declare(strict_types=1);

namespace Account\Controller;

use OutgoingApi\Service\ApiTokenService;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/account/api-token')]
final class AccountApiTokenController extends BaseController
{
    public function __construct(
        private readonly ApiTokenService $apiTokenService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/index', name: 'account_api_token_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@account/api_token.html.twig', [
            'apiToken' => $this->apiTokenService->getUserToken($this->getUser()),
        ]);
    }

    #[Route('/generate', name: 'account_api_token_generate', methods: ['POST'])]
    public function generate(Request $request): Response
    {
        $submittedToken = $request->getPayload()->get('token');

        if (false === $this->isCsrfTokenValid('generate-api-token', $submittedToken)) {
            throw $this->createAccessDeniedException();
        }

        $plainToken = $this->apiTokenService->generate($this->getUser());

        // One-time display: the flash is removed from the session as soon as the page reads it.
        $this->addFlash('api_token_plain', $plainToken);
        $this->addFlash(
            'success',
            $this->translator->trans('account.api_token.generated', [], 'front'),
        );

        return $this->redirectToRoute('account_api_token_index');
    }
}
