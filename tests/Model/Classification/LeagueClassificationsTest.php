<?php 

namespace App\Tests\Model\Classification;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Model\Classification\LeagueClassifications;
use App\Entity\UserSeason;
use App\Entity\UserSeasonRaceResults;
use App\Entity\UserSeasonPlayer;
use App\Entity\UserSeasonQualifications;

class LeagueClassificationsTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $leagueClassifications;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
        
        $this->leagueClassifications = new LeagueClassifications($league, $league->getRaces()->first()->getId());
    }

    /**
     * @dataProvider provideClassificationTypes
     */
    public function test_if_can_get_classification_based_on_type(string $type)
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType($type);

        $this->assertTrue(is_array($classification) || is_object($classification));
    }

    public function test_if_can_get_race_classification()
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('race');
       
        foreach ($classification as $key => $result) {
            $this->assertTrue($result instanceof UserSeasonRaceResults);
            $this->assertTrue($result->points > 0);
            $this->assertTrue($result->getPosition() == ($key + 1));
        }
    }

    public function test_if_can_get_players_classification()
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('players');
        
        $this->assertTrue($classification[0] instanceof UserSeasonPlayer);
        $this->assertTrue($classification[0]->points > 0);

    }

    public function test_if_can_get_qualifications_classification()
    {
        $classification = $this->leagueClassifications->getClassificationBasedOnType('qualifications');
        
        $this->assertTrue($classification[0] instanceof UserSeasonQualifications);
    }

    public function provideClassificationTypes()
    {
        return [
            ['race'],
            ['players'],
            ['qualifications'],
            ['notExistingOne']
        ];
    }
}