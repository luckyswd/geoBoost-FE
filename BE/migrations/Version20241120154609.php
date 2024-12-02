<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241120154609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE default_tag (id INT AUTO_INCREMENT NOT NULL, tags JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE holiday (id INT AUTO_INCREMENT NOT NULL, default_tag_id INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, year INT DEFAULT NULL, timezone VARCHAR(255) DEFAULT NULL, type VARCHAR(50) DEFAULT NULL, translations JSON DEFAULT NULL, holiday_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_DC9AB2345D836842 (default_tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, holiday_id INT NOT NULL, tags JSON DEFAULT NULL, INDEX IDX_389B7834D16C4DD (shop_id), INDEX IDX_389B783830A3EC0 (holiday_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_DC9AB2345D836842 FOREIGN KEY (default_tag_id) REFERENCES default_tag (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7834D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE holiday DROP FOREIGN KEY FK_DC9AB2345D836842');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7834D16C4DD');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783830A3EC0');
        $this->addSql('DROP TABLE default_tag');
        $this->addSql('DROP TABLE holiday');
        $this->addSql('DROP TABLE tag');
    }
}
