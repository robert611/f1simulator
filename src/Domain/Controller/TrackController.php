<?php

declare(strict_types=1);

namespace Domain\Controller;

use Domain\Entity\Track;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Map\InfoWindow;
use Symfony\UX\Map\Map;
use Symfony\UX\Map\Marker;
use Symfony\UX\Map\Point;

final class TrackController extends AbstractController
{
    #[Route('/track/leaflet-map/{track}', name: 'track_leaflet_map', methods: ['GET'])]
    public function leafletMap(Track $track): Response
    {
        $map = new Map('default')
            ->center(new Point((float) $track->getLatitude(), (float) $track->getLongitude()))
            ->zoom(6)
            ->addMarker(new Marker(
                position: new Point((float) $track->getLatitude(), (float) $track->getLongitude()),
                title: 'Lyon',
                infoWindow: new InfoWindow(
                    content: $track->getName(),
                )
            ));

        return $this->render('@domain/track/leaflet_map.html.twig', [
            'map' => $map,
        ]);
    }
}
