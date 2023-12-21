<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205093549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE issues_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE issues (id INT NOT NULL, title TEXT NOT NULL, description TEXT DEFAULT NULL, key VARCHAR(255) NOT NULL, key_yandex VARCHAR(255) NOT NULL, key_jira VARCHAR(255) DEFAULT NULL, id_yandex VARCHAR(255) DEFAULT NULL, id_jira VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, parent VARCHAR(255) DEFAULT NULL, aliases TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type JSON DEFAULT NULL, priority JSON DEFAULT NULL, queue JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN issues.aliases IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN issues.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE issues_id_seq CASCADE');
        $this->addSql('DROP TABLE issues');
    }
}
