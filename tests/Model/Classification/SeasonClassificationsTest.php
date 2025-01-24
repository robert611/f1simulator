<?php 

namespace App\Tests\Model\Classification;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\Classification\SeasonClassifications;
use App\Entity\Driver;
use App\Entity\Season;
use App\Entity\Qualification;
use App\Service\Configuration\RaceScoringSystem;

class SeasonClassificationsTest extends KernelTestCase 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $seasonClassifications;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();
        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);
        
        $this->seasonClassifications = new SeasonClassifications($drivers, $season, $season->getRaces()->first()->getId());
    }

    /**
     * @dataProvider provideClassificationTypes
     */
    public function test_if_get_classification_based_on_type_returns_correct_classification($type)
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType($type);
        
        $this->assertTrue(is_array($classification) || is_object($classification));
        $this->assertTrue($classification[0] instanceof Driver || $classification[0] instanceof Qualification);
    }
    
    public function test_if_get_race_classification_returns_correct_results()
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('race');
        $raceScoringSystem = (new RaceScoringSystem)->getRaceScoringSystem();

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
            $this->assertEquals($result->getPoints(), $raceScoringSystem[$result->getPosition()]);
        }
    }

    public function test_if_get_qualifications_classification_returns_correct_results()
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('qualifications');
      
        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
            $this->assertTrue($result instanceof Qualification);
        }
    }

    public function test_if_get_drivers_classification_return_correct_results()
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('drivers');
        $raceScoringSystem = (new RaceScoringSystem)->getRaceScoringSystem();

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->position, range(1, 20)));
            $this->assertEquals($result->getPoints(), 6 * $raceScoringSystem[$result->getPosition()]);
        }
    }

    public function provideClassificationTypes(): array
    {
        return [
            ['race'],
            ['drivers'],
            ['qualifications'],
            ['notExistingOne'],
        ];
    }
}