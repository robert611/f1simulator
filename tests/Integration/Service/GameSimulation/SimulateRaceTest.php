<?php

namespace App\Tests\Integration\Service\GameSimulation;

use App\Entity\Driver;
use App\Entity\Team;
use App\Model\Configuration\QualificationAdvantage;
use App\Model\Configuration\TeamsStrength;
use App\Service\GameSimulation\SimulateQualifications;
use App\Service\GameSimulation\SimulateRaceService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateRaceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private SimulateRaceService $simulateRace;
    private SimulateQualifications $simulateQualifications;
    private array $drivers;
    private array $teams;
    private array $qualificationsResults;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->drivers = $this->entityManager->getRepository(Driver::class)->findAll();
        $this->teams = $this->entityManager->getRepository(Team::class)->findAll();

        $this->simulateRace = self::getContainer()->get(SimulateRaceService::class);
        $this->simulateQualifications = self::getContainer()->get(SimulateQualifications::class);

        $this->qualificationsResults = $this->simulateQualifications->getLeagueQualificationsResults($this->drivers);
    }

    public function test_if_get_race_results_returns_correct_results()
    {
        $raceResults = $this->simulateRace->getLeagueRaceResults($this->drivers, $this->qualificationsResults);

        $this->assertTrue(count($raceResults) == count($this->drivers));

        /* Check if in results there are exactly two drivers of every team */
        foreach ($this->teams as $team) {
            $this->assertTrue(count($this->getDriversOfTeamInResults($team, $raceResults)) == 2);
        }
    }

    /* Coupons contain driverId, so although most of the code is similar to the one in getCoupons()
       method there always may be a problem with filling data */
    public function test_if_get_coupons_returns_correct_amount_of_coupons()
    {
        $expectedCoupons = 0;

        $teamsStrength = TeamsStrength::getTeamsStrength();
        $qualificationResultAdvantage = QualificationAdvantage::getQualificationResultAdvantage();

        /* Calculate Strength Of Drivers */
        foreach ($this->qualificationsResults as $position => $driver) {
            $driverTeamStrength = $teamsStrength[$driver->getTeam()->getName()];
            $driverQualificationAdvantage = $qualificationResultAdvantage[$position];

            $strength = ceil($driverTeamStrength + $driverQualificationAdvantage);

            $expectedCoupons += $strength;
        }

        $expectedCoupons *= $this->simulateRace->multiplier;

        $this->assertEquals($expectedCoupons, count($this->simulateRace->getCoupons($this->qualificationsResults)));
    }

    public function getDriversOfTeamInResults($team, $results): array
    {
        $drivers = array();

        foreach ($results as $driverId) {
            $driver = $this->entityManager->getRepository(Driver::class)->find($driverId);
            if ($driver->getTeam()->getId() == $team->getId()) {
                $drivers[] = $driver;
            }
        }

        return $drivers;
    }
}
