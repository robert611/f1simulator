<?php 

namespace App\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\SeasonClassifications;
use App\Entity\Driver;
use App\Entity\Season;
use App\Model\RacePunctation;

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
        
        $this->seasonClassifications = new SeasonClassifications($drivers, $season);
    }

    /**
     * @dataProvider provideClassificationTypes
     */
    public function test_if_get_classification_based_on_type_returns_correct_classification($type)
    {
        $classification = $this->seasonClassifications->getClassificationBasedOnType('race');
        
        $this->assertTrue(is_array($classification));
        $this->assertTrue(is_object($classification[0]));
        $this->assertTrue($classification[0] instanceof Driver);
    }
    
    public function test_if_get_last_race_results_return_correct_results()
    {
        $classification = $this->seasonClassifications->getLastRaceResults();
        $punctation = (new RacePunctation)->getPunctation();

        $isThereAUser = false;

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
            $this->assertEquals($result->getPoints(), $punctation[$result->getPosition()]);
            
            if ($result->isUser == true) $isThereAUser = true;
        }

        $this->assertTrue($isThereAUser);
    }

    public function test_if_get_last_qualifications_results_return_correct_results()
    {
        $classification = $this->seasonClassifications->getLastQualificationsResults();
      
        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->getPosition(), range(1, 20)));
        }
    }

    public function test_if_get_drivers_classification_return_correct_results()
    {
        $classification = $this->seasonClassifications->getDriversClassification();
        $punctation = (new RacePunctation)->getPunctation();

        $isThereAUser = false;

        foreach ($classification as $result) {
            $this->assertTrue(in_array($result->position, range(1, 20)));
            $this->assertEquals($result->getPoints(), 6 * $punctation[$result->getPosition()]);

            if ($result->isUser == true) $isThereAUser = true;
        }

        $this->assertTrue($isThereAUser);
    }

    public function provideClassificationTypes()
    {
        return [
            ['race'],
            ['drivers'],
            ['qualifications'],
            ['notExistingOne']
        ];
    }
}