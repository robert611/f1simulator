<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261117120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create api_token table for REST API bearer authentication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE api_token (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL,
            token_hash VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_7BA2F5EBB3BC57DA (token_hash),
            UNIQUE INDEX UNIQ_7BA2F5EBA76ED395 (user_id),
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('DROP TABLE api_token');
    }
}
