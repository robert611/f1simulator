<?php 

namespace App\Test\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\TeamStatistics\LeagueTeamsPoints;
use App\Service\Configuration\RacePunctation;
use App\Entity\UserSeason;
use App\Entity\Team;

class LeagueTeamPointsTest extends KernelTestCase
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

    public function test_if_can_get_league_team_points()
    {
        $league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
        
        $teams = (new LeagueTeamsPoints)->getTeamsPoints($league);

        foreach ($teams as $team) {
            $this->assertTrue($team instanceof Team || $team == null);
        }

        /* I count those points looking on the database */
        /* index 1 is null, because that array was filter to be unique */
        $this->assertTrue($teams[0]->points == 258);
        $this->assertTrue($teams[2]->points == 90);
        $this->assertTrue($teams[3]->points == 72);
        $this->assertTrue($teams[4]->points == 60);
    }
}