<?php 

declare(strict_types=1);

namespace App\Tests\Integration\Model\TeamStatistics;

use App\Entity\Season;
use App\Entity\Team;
use App\Model\Configuration\RaceScoringSystem;
use App\Service\TeamStatistics\TeamPoints;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamPointsTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    public function setUp(): void 
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    #[Test]
    public function it_checks_if_get_team_points_returns_correct_value(): void
    {
        $teamPointsModel = new TeamPoints();

        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        foreach ($teams as $team) {
            $points = $teamPointsModel->getTeamPoints($team, $season);

            $this->assertEquals($points, $this->getExpectedPoints($team));
        }
    }

    private function getExpectedPoints($team)
    {
        $raceScoringSystem = RaceScoringSystem::getRaceScoringSystem();
        $drivers = $team->getDrivers();

        return $raceScoringSystem[$drivers[0]->getCarNumber()] * 6 + $raceScoringSystem[$drivers[1]->getCarNumber()] * 6;
    }
}