<?php 

namespace App\Tests\Model\GameSimulation;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\GameSimulation\SimulateLeagueRace;
use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;


class SimulateLeagueRaceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function test_if_can_get_race_results()
    {
        $players = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1])->getPlayers();

        [$qualificationsResults, $raceResults] = (new SimulateLeagueRace)->getRaceResults($players);

        $this->assertTrue(count($qualificationsResults) == count($players));
        $this->assertTrue(count($raceResults) == count($players));

        for ($i = 1, $j = count($qualificationsResults); $i <= $j; $i++) {
            $this->assertTrue($qualificationsResults[$i] instanceof UserSeasonPlayer);
            $this->assertTrue($raceResults[$i] instanceof UserSeasonPlayer);
        }
    }
}