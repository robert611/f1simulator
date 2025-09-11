<?php

namespace App\Tests\Integration\Model\GameSimulation;

use App\Entity\Driver;
use App\Entity\Team;
use App\Model\Configuration\TeamsStrength;
use App\Service\GameSimulation\SimulateQualifications;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SimulateQualificationsTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private SimulateQualifications $simulateQualifications;
    private array $drivers;
    private array $teams;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->drivers = $this->entityManager->getRepository(Driver::class)->findAll();
        $this->teams = $this->entityManager->getRepository(Team::class)->findAll();

        $this->simulateQualifications = self::getContainer()->get(SimulateQualifications::class);
    }

    public function test_if_get_qualification_results_returns_correct_results()
    {
        $results = $this->simulateQualifications->getLeagueQualificationsResults($this->drivers);

        /* Array indexes start from 1 */
        $this->assertTrue(count($results) == count($this->drivers));
        $this->assertTrue($results[1] instanceof Driver);

        /* Check if in results there are exactly two drivers of every team */
        foreach ($this->teams as $team) {
            $this->assertTrue(count($this->getDriversOfTeamInResults($team, $results)) == 2);
        }
    }

    /* Coupons contain teamName, so altough most of the code is similar to the one in getCoupons()
     method there always may be a problem with filling data */
    public function test_if_get_coupons_returns_correct_amount_of_coupons()
    {
        $expectedCoupons = 0;

        $teamsStrength = TeamsStrength::getTeamsStrength();

        foreach ($teamsStrength as $strength) {
            $expectedCoupons += ceil($strength);
        }

        $expectedCoupons *= $this->simulateQualifications->multiplier;

        $this->assertEquals($expectedCoupons, count($this->simulateQualifications->getCoupons()));
    }

    public function test_if_draw_driver_from_team_draws_correct_driver()
    {
        $teamName = $this->teams[0]->getName();

        $driver = $this->simulateQualifications->drawDriverFromTeam($teamName, $this->drivers, []);

        $this->assertEquals($driver->getTeam()->getName(), $teamName);
    }

    public function test_if_check_if_both_drivers_from_team_already_finished_works_correctly()
    {
        $teamName = $this->teams[0]->getName();

        $checkTrue = $this->simulateQualifications->checkIfBothDriversFromTeamAlreadyFinished(
            $teamName,
            $this->drivers,
        );
        $checkFalse = $this->simulateQualifications->checkIfBothDriversFromTeamAlreadyFinished($teamName, []);

        $this->assertTrue($checkTrue);
        $this->assertFalse($checkFalse);
    }

    public function getDriversOfTeamInResults($team, $results)
    {
        $drivers = array();

        foreach ($results as $result) {
            if ($result->getTeam()->getId() == $team->getId()) {
                $drivers[] = $result;
            }
        }

        return $drivers;
    }
}
