<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415094729 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_F0E45BA9C3423909 ON season (driver_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user_season CHANGE started started TINYINT(1) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE race_results CHANGE driver_id driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9C3423909');
        $this->addSql('DROP INDEX IDX_F0E45BA9C3423909 ON season');
        $this->addSql('ALTER TABLE season ADD team_id INT DEFAULT NULL, CHANGE driver_id car_id INT NOT NULL');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_F0E45BA9296CD8AE ON season (team_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE user_season CHANGE started started TINYINT(1) DEFAULT \'NULL\'');
    }
}
