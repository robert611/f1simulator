<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200325190117 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qualification DROP FOREIGN KEY FK_B712F0CE6E59D40D');
        $this->addSql('DROP INDEX UNIQ_B712F0CE6E59D40D ON qualification');
        $this->addSql('ALTER TABLE qualification ADD race SMALLINT NOT NULL, DROP race_id');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE qualification ADD race_id INT NOT NULL, DROP race');
        $this->addSql('ALTER TABLE qualification ADD CONSTRAINT FK_B712F0CE6E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B712F0CE6E59D40D ON qualification (race_id)');
        $this->addSql('ALTER TABLE season CHANGE team_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
