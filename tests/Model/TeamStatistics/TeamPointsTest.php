<?php 

namespace App\Test\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\TeamStatistics\TeamPoints;
use App\Model\Configuration\RacePunctation;
use App\Entity\Driver;
use App\Entity\RaceResult;
use App\Entity\Team;
use App\Entity\Season;

class TeamPointsTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function setUp(): void 
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function test_if_get_team_points_return_team_points()
    {
        $driversRepository = $this->entityManager->getRepository(Driver::class);
        $raceResultsRepository = $this->entityManager->getRepository(RaceResult::class);

        $teamPointsModel = new TeamPoints($raceResultsRepository);

        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        foreach ($teams as $team) {
            $points = $teamPointsModel->getTeamPoints($team, $season);

            $this->assertEquals($points, $this->getExpectedPoints($team));
        }
    }

    private function getExpectedPoints($team)
    {
        $racePunctation = (new RacePunctation())->getPunctation();
        $drivers = $team->getDrivers();

        return $racePunctation[$drivers[0]->getCarId()] * 6 + $racePunctation[$drivers[1]->getCarId()] * 6;  
    }
}