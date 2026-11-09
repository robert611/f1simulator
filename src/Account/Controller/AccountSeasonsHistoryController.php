<?php

declare(strict_types=1);

namespace Account\Controller;

use Computer\ComputerFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account/seasons-history')]
final class AccountSeasonsHistoryController extends BaseController
{
    public function __construct(
        private readonly ComputerFacadeInterface $computerFacade,
    ) {
    }

    #[Route('/index', name: 'account_seasons_history', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $userSeasons = $this->computerFacade->getUserSeasons($this->getUser()->getId());

        return $this->render('@account/seasons_history.html.twig', [
            'userSeasons' => $userSeasons,
        ]);
    }
}
