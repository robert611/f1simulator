<?php 

namespace App\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\DriverPoints;
use App\Entity\Driver;
use App\Entity\Season;
use App\Model\RacePunctation;

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

        $punctation = (new RacePunctation)->getPunctation();

        /* According to test database drivers order reflects driver position */
        foreach ($drivers as $key => $driver) {
            $points = $this->driverPoints->getDriverPoints($driver, $season);

            $expectedPoints = 6 * $punctation[$key + 1];

            $this->assertEquals($points, $expectedPoints);
            
        }

        /* According to test database driver with first id always has first place in any race */
        foreach ($season->getRaces() as $race) {
            $pointsByRace = $this->driverPoints->getDriverPointsByRace($drivers[0], $race);

            $this->assertEquals($pointsByRace, 25);
        }
    }
}