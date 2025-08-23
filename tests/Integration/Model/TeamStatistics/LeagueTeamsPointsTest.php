<?php 

namespace App\Tests\Integration\Model\TeamStatistics;

use App\Entity\Team;
use App\Entity\UserSeason;
use App\Service\TeamStatistics\LeagueTeamsPoints;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueTeamPointsTest extends KernelTestCase
{
    private EntityManager $entityManager;
    private LeagueTeamsPoints $leagueTeamsPoints;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->leagueTeamsPoints = self::getContainer()->get(LeagueTeamsPoints::class);
    }

    public function test_if_can_get_league_team_points()
    {
        $league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
        
        $teams = $this->leagueTeamsPoints->getTeamsPoints($league);

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