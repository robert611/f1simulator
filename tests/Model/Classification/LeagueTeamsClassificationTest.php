<?php 

namespace App\Tests\Model\Classification;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\Classification\LeagueTeamsClassification;
use App\Entity\UserSeason;
use App\Entity\Team;

class LeagueTeamsClassificationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private LeagueTeamsClassification $leagueTeamsClassification;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->leagueTeamsClassification = self::getContainer()->get(LeagueTeamsClassification::class);
    }

    public function test_if_can_get_teams_classification()
    {
        $league = $this->entityManager->getRepository(UserSeason::class)->findOneBy(['completed' => 1]);
      
        $classification = $this->leagueTeamsClassification->getClassification($league);

        foreach ($classification as $key => $team) {
            $this->assertTrue($team instanceof Team);
            $this->assertTrue($team->getPoints() >= (isset($classification[$key + 1]) ? $classification[$key + 1]->getPoints() : 0));
        }

        /* I have count it looking on the database */
        /* Ferrari|Williams|Mercedes|Renault */
        $this->assertTrue(count($classification) == 4);

        /* I have count it, looking on results in database */
        $this->assertTrue($classification[0]->getPoints() == 258);
        $this->assertTrue($classification[1]->getPoints() == 90);
        $this->assertTrue($classification[2]->getPoints() == 72);
        $this->assertTrue($classification[3]->getPoints() == 60);
    }
}