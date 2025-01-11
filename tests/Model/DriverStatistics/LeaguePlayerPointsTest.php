<?php 

namespace App\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\DriverStatistics\LeaguePlayerPoints;
use App\Service\Configuration\RacePunctation;
use App\Entity\UserSeason;

class LeaguePlayerPointsTest extends KernelTestCase 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $leaguePlayerPoints;
    private object $league;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
        $this->leaguePlayerPoints = new LeaguePlayerPoints();
    }

    public function test_if_can_get_player_points()
    {
        $punctation = (new RacePunctation)->getPunctation();

        /* I have count it looking on the database */
        $this->assertTrue($this->leaguePlayerPoints->getPlayerPoints($this->league->getPlayers()[0]) == 150);
        $this->assertTrue($this->leaguePlayerPoints->getPlayerPoints($this->league->getPlayers()[1]) == 108);
        $this->assertTrue($this->leaguePlayerPoints->getPlayerPoints($this->league->getPlayers()[2]) == 90);
        $this->assertTrue($this->leaguePlayerPoints->getPlayerPoints($this->league->getPlayers()[3]) == 72);
        $this->assertTrue($this->leaguePlayerPoints->getPlayerPoints($this->league->getPlayers()[4]) == 60);
    }

    public function test_if_can_get_player_points_by_result()
    {
        $this->league->getRaces()->first()->getRaceResults()->map(function($result) {
            $points = $this->leaguePlayerPoints->getPlayerPointsByResult($result);

            $this->assertTrue($points > 0);
        });
    }
}