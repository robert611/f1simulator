<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Season;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Track;
use App\Entity\Driver;
use App\Model\DriverPoints;

class IndexController extends AbstractController
{
    /**
     * @Route("/home/{classificationType}", name="app_index")
     */
    public function index($classificationType = 'race')
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/IndexController.php',
        ]);
    }
}
