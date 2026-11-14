<?php

declare(strict_types=1);

namespace Account\Controller;

use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account/api-token')]
final class AccountApiTokenController extends BaseController
{
    #[Route('/index', name: 'account_api_token_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@account/api_token.html.twig');
    }
}
