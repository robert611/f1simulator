<?php 

namespace App\Tests\Integration\Model\DriverStatistics;

use App\Entity\UserSeason;
use App\Service\DriverStatistics\FillLeaguePlayerData;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FillLeagueUserDataTest extends KernelTestCase 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $league;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
    }
    
    public function test_if_can_get_player()
    {
        $fillLeaguePlayerData = new FillLeaguePlayerData($this->league->getPlayers()->first(), $this->league);

        $player = $fillLeaguePlayerData->getPlayer();

        $this->assertTrue($player->points == 150);
        $this->assertTrue($player->podiums[1] == 6);
        $this->assertTrue($player->podiums[2] == 0);
        $this->assertTrue($player->podiums[3] == 0);
    }
}