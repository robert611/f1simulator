<?php 

namespace App\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\DriverStatistics\DriverPodiumsService;
use App\Entity\Driver;
use App\Entity\Season;

class DriverPodiumsTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public object $driverPodiums;

    public function setUp(): void 
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->driverPodiums = new DriverPodiumsService();
    }

    public function test_if_get_driver_podiums_returns_driver_podiums()
    {
        $drivers = $driversRepository = $this->entityManager->getRepository(Driver::class)->findAll();
        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);
        $podiumsTable = $this->driverPodiums->getPodiumsTable();

        foreach ($drivers as $key => $driver) {
            $podiums = $this->driverPodiums->getDriverPodiums($driver, $season);

            $this->assertEquals($podiums, $this->getExpectedPodiums($key + 1, $podiumsTable));
        }
    }

    public function getExpectedPodiums($driverId, $podiumsTable)
    {
        if ($driverId >= 1 && $driverId <= 3)  $podiumsTable[$driverId] += 6;

        return $podiumsTable;
    }
}