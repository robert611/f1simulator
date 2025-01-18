<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250118083454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, team_id INT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, car_id INT NOT NULL, INDEX IDX_11667CD9296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qualification (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, race_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_B712F0CEC3423909 (driver_id), INDEX IDX_B712F0CE6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, track_id INT NOT NULL, season_id INT NOT NULL, INDEX IDX_DA6FBBAF5ED23C43 (track_id), INDEX IDX_DA6FBBAF4EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE race_result (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, driver_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_793CDFC06E59D40D (race_id), INDEX IDX_793CDFC0C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE season (id INT AUTO_INCREMENT NOT NULL, driver_id INT NOT NULL, user_id INT NOT NULL, completed TINYINT(1) NOT NULL, INDEX IDX_F0E45BA9C3423909 (driver_id), INDEX IDX_F0E45BA9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, picture VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, picture VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, secret VARCHAR(255) NOT NULL, max_players SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, completed TINYINT(1) NOT NULL, started TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_95B640945CA2E8E5 (secret), UNIQUE INDEX UNIQ_95B640945E237E06 (name), INDEX IDX_95B640947E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_player (id INT AUTO_INCREMENT NOT NULL, season_id INT NOT NULL, user_id INT NOT NULL, driver_id INT NOT NULL, INDEX IDX_60EC29874EC001D1 (season_id), INDEX IDX_60EC2987A76ED395 (user_id), INDEX IDX_60EC2987C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_qualification (id INT AUTO_INCREMENT NOT NULL, player_id INT NOT NULL, race_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_6A31DCCC99E6F5DF (player_id), INDEX IDX_6A31DCCC6E59D40D (race_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_race (id INT AUTO_INCREMENT NOT NULL, track_id INT NOT NULL, season_id INT NOT NULL, INDEX IDX_F53DEEA45ED23C43 (track_id), INDEX IDX_F53DEEA44EC001D1 (season_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_season_race_result (id INT AUTO_INCREMENT NOT NULL, race_id INT NOT NULL, player_id INT NOT NULL, position SMALLINT NOT NULL, INDEX IDX_9DFA31BD6E59D40D (race_id), INDEX IDX_9DFA31BD99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver ADD CONSTRAINT FK_11667CD9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE qualification ADD CONSTRAINT FK_B712F0CEC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE qualification ADD CONSTRAINT FK_B712F0CE6E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAF5ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAF4EC001D1 FOREIGN KEY (season_id) REFERENCES season (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_793CDFC06E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE race_result ADD CONSTRAINT FK_793CDFC0C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_season ADD CONSTRAINT FK_95B640947E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_season_player ADD CONSTRAINT FK_60EC29874EC001D1 FOREIGN KEY (season_id) REFERENCES user_season (id)');
        $this->addSql('ALTER TABLE user_season_player ADD CONSTRAINT FK_60EC2987A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_season_player ADD CONSTRAINT FK_60EC2987C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE user_season_qualification ADD CONSTRAINT FK_6A31DCCC99E6F5DF FOREIGN KEY (player_id) REFERENCES user_season_player (id)');
        $this->addSql('ALTER TABLE user_season_qualification ADD CONSTRAINT FK_6A31DCCC6E59D40D FOREIGN KEY (race_id) REFERENCES user_season_race (id)');
        $this->addSql('ALTER TABLE user_season_race ADD CONSTRAINT FK_F53DEEA45ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE user_season_race ADD CONSTRAINT FK_F53DEEA44EC001D1 FOREIGN KEY (season_id) REFERENCES user_season (id)');
        $this->addSql('ALTER TABLE user_season_race_result ADD CONSTRAINT FK_9DFA31BD6E59D40D FOREIGN KEY (race_id) REFERENCES user_season_race (id)');
        $this->addSql('ALTER TABLE user_season_race_result ADD CONSTRAINT FK_9DFA31BD99E6F5DF FOREIGN KEY (player_id) REFERENCES user_season_player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9296CD8AE');
        $this->addSql('ALTER TABLE qualification DROP FOREIGN KEY FK_B712F0CEC3423909');
        $this->addSql('ALTER TABLE qualification DROP FOREIGN KEY FK_B712F0CE6E59D40D');
        $this->addSql('ALTER TABLE race DROP FOREIGN KEY FK_DA6FBBAF5ED23C43');
        $this->addSql('ALTER TABLE race DROP FOREIGN KEY FK_DA6FBBAF4EC001D1');
        $this->addSql('ALTER TABLE race_result DROP FOREIGN KEY FK_793CDFC06E59D40D');
        $this->addSql('ALTER TABLE race_result DROP FOREIGN KEY FK_793CDFC0C3423909');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9C3423909');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9A76ED395');
        $this->addSql('ALTER TABLE user_season DROP FOREIGN KEY FK_95B640947E3C61F9');
        $this->addSql('ALTER TABLE user_season_player DROP FOREIGN KEY FK_60EC29874EC001D1');
        $this->addSql('ALTER TABLE user_season_player DROP FOREIGN KEY FK_60EC2987A76ED395');
        $this->addSql('ALTER TABLE user_season_player DROP FOREIGN KEY FK_60EC2987C3423909');
        $this->addSql('ALTER TABLE user_season_qualification DROP FOREIGN KEY FK_6A31DCCC99E6F5DF');
        $this->addSql('ALTER TABLE user_season_qualification DROP FOREIGN KEY FK_6A31DCCC6E59D40D');
        $this->addSql('ALTER TABLE user_season_race DROP FOREIGN KEY FK_F53DEEA45ED23C43');
        $this->addSql('ALTER TABLE user_season_race DROP FOREIGN KEY FK_F53DEEA44EC001D1');
        $this->addSql('ALTER TABLE user_season_race_result DROP FOREIGN KEY FK_9DFA31BD6E59D40D');
        $this->addSql('ALTER TABLE user_season_race_result DROP FOREIGN KEY FK_9DFA31BD99E6F5DF');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE qualification');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE race_result');
        $this->addSql('DROP TABLE season');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE track');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_season');
        $this->addSql('DROP TABLE user_season_player');
        $this->addSql('DROP TABLE user_season_qualification');
        $this->addSql('DROP TABLE user_season_race');
        $this->addSql('DROP TABLE user_season_race_result');
    }
}
