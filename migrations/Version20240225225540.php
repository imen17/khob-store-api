<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225225540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP CONSTRAINT fk_64c19c1796a8f92');
        $this->addSql('DROP INDEX idx_64c19c1796a8f92');
        $this->addSql('ALTER TABLE category RENAME COLUMN parent_category_id TO parent_id');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1727ACA70 FOREIGN KEY (parent_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1727ACA70 ON category (parent_id)');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT fk_d34a04ad12469de2');
        $this->addSql('DROP INDEX idx_d34a04ad12469de2');
        $this->addSql('ALTER TABLE product DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE category DROP CONSTRAINT FK_64C19C1727ACA70');
        $this->addSql('DROP INDEX UNIQ_64C19C1727ACA70');
        $this->addSql('ALTER TABLE category RENAME COLUMN parent_id TO parent_category_id');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT fk_64c19c1796a8f92 FOREIGN KEY (parent_category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_64c19c1796a8f92 ON category (parent_category_id)');
        $this->addSql('ALTER TABLE product ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT fk_d34a04ad12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d34a04ad12469de2 ON product (category_id)');
    }
}
