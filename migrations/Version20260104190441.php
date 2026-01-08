<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260104190441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_11667CD98EF8F3A6 ON driver (car_number)');
        $this->addSql('ALTER TABLE season DROP FOREIGN KEY FK_F0E45BA9C3423909');
        $this->addSql('DROP INDEX IDX_F0E45BA9C3423909 ON season');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_11667CD98EF8F3A6 ON driver');
        $this->addSql('ALTER TABLE season ADD CONSTRAINT FK_F0E45BA9C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('CREATE INDEX IDX_F0E45BA9C3423909 ON season (driver_id)');
    }
}
