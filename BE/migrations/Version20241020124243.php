<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020124243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday ADD default_tag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB2345D836842 FOREIGN KEY (default_tag_id) REFERENCES default_tag (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DC9AB2345D836842 ON holiday (default_tag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB2345D836842');
        $this->addSql('DROP INDEX UNIQ_DC9AB2345D836842 ON holiday');
        $this->addSql('ALTER TABLE holiday DROP default_tag_id');
    }
}
