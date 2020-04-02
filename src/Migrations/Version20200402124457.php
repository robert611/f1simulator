<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200402124457 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_season (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, secret VARCHAR(255) NOT NULL, max_players SMALLINT NOT NULL, INDEX IDX_95B640947E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_players (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, user_id INT NOT NULL, driver_id INT NOT NULL, INDEX IDX_68B235A14EC001D1 (season_id), INDEX IDX_68B235A1A76ED395 (user_id), INDEX IDX_68B235A1C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_qualifications (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, race_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_89B6704C99E6F5DF (player_id), INDEX IDX_89B6704C6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_race_results (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, player_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_AE43FA0B6E59D40D (race_id), INDEX IDX_AE43FA0B99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_races (id INT AUTO_INCREMENT NOT NULL, track_id INT NOT NULL, user_season_id INT NOT NULL, INDEX IDX_CA4095145ED23C43 (track_id), INDEX IDX_CA4095144E208C35 (user_season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_season ADD CONSTRAINT FK_95B640947E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_season_players ADD CONSTRAINT FK_68B235A14EC001D1 FOREIGN KEY (season_id) REFERENCES user_season (id)');
        $this->addSql('ALTER TABLE user_season_players ADD CONSTRAINT FK_68B235A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_season_players ADD CONSTRAINT FK_68B235A1C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE user_season_qualifications ADD CONSTRAINT FK_89B6704C99E6F5DF FOREIGN KEY (player_id) REFERENCES user_season_players (id)');
        $this->addSql('ALTER TABLE user_season_qualifications ADD CONSTRAINT FK_89B6704C6E59D40D FOREIGN KEY (race_id) REFERENCES user_season_races (id)');
        $this->addSql('ALTER TABLE user_season_race_results ADD CONSTRAINT FK_AE43FA0B6E59D40D FOREIGN KEY (race_id) REFERENCES user_season_races (id)');
        $this->addSql('ALTER TABLE user_season_race_results ADD CONSTRAINT FK_AE43FA0B99E6F5DF FOREIGN KEY (player_id) REFERENCES user_season_players (id)');
        $this->addSql('ALTER TABLE user_season_races ADD CONSTRAINT FK_CA4095145ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE user_season_races ADD CONSTRAINT FK_CA4095144E208C35 FOREIGN KEY (user_season_id) REFERENCES user_season (id)');
        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_season_players DROP FOREIGN KEY FK_68B235A14EC001D1');
        $this->addSql('ALTER TABLE user_season_races DROP FOREIGN KEY FK_CA4095144E208C35');
        $this->addSql('ALTER TABLE user_season_qualifications DROP FOREIGN KEY FK_89B6704C99E6F5DF');
        $this->addSql('ALTER TABLE user_season_race_results DROP FOREIGN KEY FK_AE43FA0B99E6F5DF');
        $this->addSql('ALTER TABLE user_season_qualifications DROP FOREIGN KEY FK_89B6704C6E59D40D');
        $this->addSql('ALTER TABLE user_season_race_results DROP FOREIGN KEY FK_AE43FA0B6E59D40D');
        $this->addSql('DROP TABLE user_season');
        $this->addSql('DROP TABLE user_season_players');
        $this->addSql('DROP TABLE user_season_qualifications');
        $this->addSql('DROP TABLE user_season_race_results');
        $this->addSql('DROP TABLE user_season_races');
        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
