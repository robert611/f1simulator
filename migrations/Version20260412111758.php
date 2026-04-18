<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260412111758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_season MODIFY completed TINYINT(1) NOT NULL AFTER started');
        $this->addSql('ALTER TABLE user_season ADD started_at DATETIME DEFAULT NULL, ADD completed_at DATETIME DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE user_season SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL');
        $this->addSql('ALTER TABLE user_season MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE season ADD completed_at DATETIME DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE season SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL');
        $this->addSql('ALTER TABLE season MODIFY created_at DATETIME NOT NULL, MODIFY updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_season MODIFY completed TINYINT(1) NOT NULL AFTER name');
        $this->addSql('ALTER TABLE user_season DROP started_at, DROP completed_at, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE season DROP completed_at, DROP created_at, DROP updated_at');
    }
}
