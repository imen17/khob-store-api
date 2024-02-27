<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226000829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE address DROP CONSTRAINT fk_d4e6f819d86650f');
        $this->addSql('DROP INDEX idx_d4e6f819d86650f');
        $this->addSql('ALTER TABLE address RENAME COLUMN user_id_id TO owner_id');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F817E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D4E6F817E3C61F9 ON address (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE address DROP CONSTRAINT FK_D4E6F817E3C61F9');
        $this->addSql('DROP INDEX IDX_D4E6F817E3C61F9');
        $this->addSql('ALTER TABLE address RENAME COLUMN owner_id TO user_id_id');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT fk_d4e6f819d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d4e6f819d86650f ON address (user_id_id)');
    }
}
