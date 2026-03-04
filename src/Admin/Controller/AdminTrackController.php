<?php

declare(strict_types=1);

namespace Admin\Controller;

use Domain\DomainFacadeInterface;
use Shared\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin-track')]
class AdminTrackController extends BaseController
{
    public function __construct(
        private readonly DomainFacadeInterface $domainFacade,
    ) {
    }

    #[Route('', name: 'admin_track_index', methods: ['GET'])]
    public function index(): Response
    {
        $tracks = $this->domainFacade->getAllTracks();

        return $this->render('@admin/admin_track/index.html.twig', [
            'tracks' => $tracks,
        ]);
    }
}
