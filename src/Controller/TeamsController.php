<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Team;
use App\Entity\Season;
use App\Entity\Driver;
use App\Entity\RaceResults;
use App\Model\TeamPoints;

class TeamsController extends AbstractController
{
    /**
     * @Route("/teams", name="teams")
     */
    public function index()
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();

        return new JsonResponse($teams);
    }

    /**
     * @Route("/teams/classification", name="teams_classification")
     */
    public function getTeamsClassification()
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)->findAll();
        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['user' => $this->getUser()->getId(), 'completed' => 0]);

        $raceResultsRepository = $this->getDoctrine()->getRepository(RaceResults::class);
        $driversRepository = $this->getDoctrine()->getRepository(Driver::class);

        /* In default teams have no assign points got in current season in database, so it has to be done here */
        foreach($teams as &$team) {
            $points = $season ? (new TeamPoints($raceResultsRepository))->getTeamPoints($team, $season) : 0;
            $team->setPoints($points);
        }

        /* Sort Teams according to it's got points */
        usort($teams, function($a, $b) {
            return $a->getPoints() < $b->getPoints();
        });
        
        foreach($teams as $key => &$team) {
            $team->setPosition($key + 1);
        }

        return new JsonResponse($teams);
    }
}
