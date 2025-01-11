<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250111182629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE race_results RENAME race_result');
        $this->addSql('ALTER TABLE user_season_races RENAME user_season_race');
        $this->addSql('ALTER TABLE user_season_players RENAME user_season_player');
        $this->addSql('ALTER TABLE user_season_qualifications RENAME user_season_qualification');
        $this->addSql('ALTER TABLE user_season_race_results RENAME user_season_race_result');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
