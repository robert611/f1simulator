<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200411102952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_season CHANGE started started TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user_season_races DROP FOREIGN KEY FK_CA4095144E208C35');
        $this->addSql('DROP INDEX IDX_CA4095144E208C35 ON user_season_races');
        $this->addSql('ALTER TABLE user_season_races CHANGE user_season_id season_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_season_races ADD CONSTRAINT FK_CA4095144EC001D1 FOREIGN KEY (season_id) REFERENCES user_season (id)');
        $this->addSql('CREATE INDEX IDX_CA4095144EC001D1 ON user_season_races (season_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_season CHANGE started started TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user_season_races DROP FOREIGN KEY FK_CA4095144EC001D1');
        $this->addSql('DROP INDEX IDX_CA4095144EC001D1 ON user_season_races');
        $this->addSql('ALTER TABLE user_season_races CHANGE season_id user_season_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_season_races ADD CONSTRAINT FK_CA4095144E208C35 FOREIGN KEY (user_season_id) REFERENCES user_season (id)');
        $this->addSql('CREATE INDEX IDX_CA4095144E208C35 ON user_season_races (user_season_id)');
    }
}
