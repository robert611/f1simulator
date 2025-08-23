<?php 

namespace App\Tests\Integration\Model\DriverStatistics;

use App\Entity\Driver;
use App\Entity\Season;
use App\Model\Configuration\RaceScoringSystem;
use App\Service\DriverStatistics\DriverPoints;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DriverPointsTest extends KernelTestCase 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public object $driverPoints;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->driverPoints = new DriverPoints();
    }

    public function test_if_get_driver_points_returns_driver_points()
    {
        $driversRepository = $this->entityManager->getRepository(Driver::class);
        $drivers = $driversRepository->findAll();

        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);

        $raceScoringSystem = RaceScoringSystem::getRaceScoringSystem();

        /* According to test database drivers order reflects driver position */
        foreach ($drivers as $key => $driver) {
            $points = $this->driverPoints->getDriverPoints($driver, $season);

            $expectedPoints = 6 * $raceScoringSystem[$key + 1];

            $this->assertEquals($points, $expectedPoints);
            
        }

        /* According to test database driver with first id always has first place in any race */
        foreach ($season->getRaces() as $race) {
            $pointsByRace = $this->driverPoints->getDriverPointsByRace($drivers[0], $race);

            $this->assertEquals($pointsByRace, 25);
        }
    }
}