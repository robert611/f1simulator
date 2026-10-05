<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261005144131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE change_email_confirmation_token (id INT AUTO_INCREMENT NOT NULL, new_email VARCHAR(255) NOT NULL, token VARCHAR(180) NOT NULL, is_valid TINYINT NOT NULL, expiry_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_EEC73035F37A13B (token), INDEX IDX_EEC7303A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE change_email_confirmation_token ADD CONSTRAINT FK_EEC7303A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE change_email_confirmation_token DROP FOREIGN KEY FK_EEC7303A76ED395');
        $this->addSql('DROP TABLE change_email_confirmation_token');
    }
}
