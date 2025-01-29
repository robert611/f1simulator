<?php 

namespace App\Tests\Model\Classification;

use App\Tests\Common\Fixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\Classification\SeasonTeamsClassification;
use App\Entity\Team;

class SeasonTeamsClassificationTest extends KernelTestCase
{
    private SeasonTeamsClassification $seasonTeamsClassification;
    private Fixtures $fixtures;

    public function setUp(): void
    {
        $this->seasonTeamsClassification = self::getContainer()->get(SeasonTeamsClassification::class);
        $this->fixtures = self::getContainer()->get(Fixtures::class);
    }

    public function test_if_can_get_teams_classification(): void
    {
        // given
        $user = $this->fixtures->aUser();
        $team = $this->fixtures->aTeam();
        $driver = $this->fixtures->aDriver('Robert', 'Kubica', $team, '88');
        $season = $this->fixtures->aSeason($user, $driver);

        $classification = $this->seasonTeamsClassification->getClassification($user->getId());

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