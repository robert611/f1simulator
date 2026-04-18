<?php

declare(strict_types=1);

namespace Admin\Controller;

use Computer\ComputerFacadeInterface;
use Multiplayer\MultiplayerFacadeInterface;
use Security\Contract\UserCountryServiceInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends BaseController
{
    public function __construct(
        private readonly UserCountryServiceInterface $userCountryService,
        private readonly ComputerFacadeInterface $computerFacade,
        private readonly MultiplayerFacadeInterface $multiplayerFacade,
    ) {
    }

    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function admin(): Response
    {
        return $this->render('@admin/dashboard.html.twig');
    }

    #[Route('/user-country-map', name: 'admin_user_country_map', methods: ['GET'])]
    public function userCountryMap(): Response
    {
        $userCountryMapData = $this->userCountryService->getUserCountryMapData();

        return $this->render('@admin/dashboard/user_country_map.html.twig', [
            'userCountryMapData' => $userCountryMapData,
        ]);
    }

    #[Route('/seasons-played-chart', name: 'admin_seasons_played_chart', methods: ['GET'])]
    public function seasonsPlayedChart(): Response
    {
        $computerSeasonsPlayed = $this->computerFacade->getLast12MonthsSeasonPlayed();
        $multiplayerSeasonsPlayed = $this->multiplayerFacade->getLast12MonthsSeasonPlayed();

        return $this->render('@admin/dashboard/seasons_played_chart.html.twig', [
            'seasonsPlayedChartData' => [
                'computer' => $computerSeasonsPlayed,
                'multiplayer' => $multiplayerSeasonsPlayed,
            ],
        ]);
    }
}
