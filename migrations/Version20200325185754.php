<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325185754 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qualification DROP FOREIGN KEY FK_B712F0CEC3423909');
        $this->addSql('DROP INDEX IDX_B712F0CEC3423909 ON qualification');
        $this->addSql('ALTER TABLE qualification ADD driver SMALLINT NOT NULL, DROP driver_id');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qualification ADD driver_id INT NOT NULL, DROP driver');
        $this->addSql('ALTER TABLE qualification ADD CONSTRAINT FK_B712F0CEC3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_B712F0CEC3423909 ON qualification (driver_id)');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
