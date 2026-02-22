<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260216195505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            ALTER TABLE user 
            ADD is_verified TINYINT(1) NOT NULL DEFAULT 0,
            ADD created_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
            ADD updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
        ");

        $this->addSql("
            UPDATE user 
            SET created_at = NOW(), updated_at = NOW()
            WHERE created_at IS NULL
        ");

        $this->addSql("
            ALTER TABLE user 
            MODIFY created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            MODIFY updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP is_verified, DROP created_at, DROP updated_at');
    }
}
