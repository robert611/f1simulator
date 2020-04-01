<?php 

namespace App\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\SimulateQualifications;
use App\Model\SimulateRace;
use App\Model\TeamsStrength;
use App\Entity\Driver;
use App\Entity\Team;

class SimulateRaceTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $simulateRace;
    private object $simulateQualifications;
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

        $this->simulateRace = new SimulateRace();
        $this->simulateQualifications = new SimulateQualifications();

        $this->qualificationsResults = $this->simulateQualifications->getQualificationsResults($this->drivers);
    }

    public function test_if_get_race_results_returns_correct_results()
    {
        $raceResults = $this->simulateRace->getRaceResults($this->drivers, $this->qualificationsResults);

        $this->assertTrue(count($raceResults) == count($this->drivers));

        /* Check if in results there are exactly two drivers of every team */
        foreach ($this->teams as $team) {
            $this->assertTrue(count($this->getDriversOfTeamInResults($team, $raceResults)) == 2);
        }
    }

    /* Coupons contain driverId, so altough most of the code is similar to the one in getCoupons() method there always may be a problem with filling data */
    public function test_if_get_coupons_returns_correct_amount_of_coupons()
    {
        $expectedCoupons = 0;

        $teamsStrength = (new TeamsStrength)->getTeamsStrength();
        $qualificationResultAdvantage = $this->simulateRace->getQualificationResultAdvantage();

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
    
    public function getDriversOfTeamInResults($team, $results)
    {
        $drivers = array();

        foreach ($results as $driverId) {
            $driver = $this->entityManager->getRepository(Driver::class)->find($driverId);
            if($driver->getTeam()->getId() == $team->getId()) $drivers[] = $driver;
        }

        return $drivers;
    }
}