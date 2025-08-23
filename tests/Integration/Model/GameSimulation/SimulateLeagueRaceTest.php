<?php 

namespace App\Tests\Integration\Model\GameSimulation;

use App\Entity\UserSeason;
use App\Entity\UserSeasonPlayer;
use App\Service\GameSimulation\SimulateLeagueRace;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class SimulateLeagueRaceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SimulateLeagueRace $simulateLeagueRace;
    
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->simulateLeagueRace = self::getContainer()->get(SimulateLeagueRace::class);
    }

    public function test_if_can_get_race_results()
    {
        $players = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1])->getPlayers();

        [$qualificationsResults, $raceResults] = $this->simulateLeagueRace->getRaceResults($players);

        $this->assertTrue(count($qualificationsResults) == count($players));
        $this->assertTrue(count($raceResults) == count($players));

        for ($i = 1, $j = count($qualificationsResults); $i <= $j; $i++) {
            $this->assertTrue($qualificationsResults[$i] instanceof UserSeasonPlayer);
            $this->assertTrue($raceResults[$i] instanceof UserSeasonPlayer);
        }
    }
}