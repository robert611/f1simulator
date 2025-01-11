<?php 

namespace App\Tests\Model\Classification;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\Classification\SeasonTeamsClassification;
use App\Entity\Team;
use App\Entity\Season;

class SeasonTeamsClassificationTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private object $seasonTeamsClassification;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->seasonTeamsClassification = new SeasonTeamsClassification();
    }

    public function test_if_can_get_teams_classification()
    {
        $season = $this->entityManager->getRepository(Season::class)->findOneBy(['completed' => 1]);
        $teams = $this->entityManager->getRepository(Team::class)->findAll();

        $classification = $this->seasonTeamsClassification->getClassification($teams, $season);

        foreach ($classification as $key => $team) {
            $this->assertTrue($team instanceof Team);
            $this->assertTrue($team->getPoints() >= (isset($classification[$key + 1]) ? $classification[$key + 1]->getPoints() : 0));
        }

        $this->assertTrue(count($classification) == 10);

        /* I have count it, looking on results in database */
        $this->assertTrue($classification[0]->getPoints() == 258);
        $this->assertTrue($classification[1]->getPoints() == 162);
        $this->assertTrue($classification[2]->getPoints() == 108);

        /* ... */
        $this->assertTrue($classification[9]->getPoints() == 0);
    }
}