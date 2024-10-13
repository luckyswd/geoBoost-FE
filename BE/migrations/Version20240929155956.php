<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240929155956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shop (id INT AUTO_INCREMENT NOT NULL, sub_shop_id INT DEFAULT NULL, domain VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, country_code VARCHAR(3) DEFAULT NULL, country_name VARCHAR(255) DEFAULT NULL, language VARCHAR(3) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT 0, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_AC6A4CA2A7A91E0B (domain), INDEX IDX_AC6A4CA232CE9829 (sub_shop_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shop ADD CONSTRAINT FK_AC6A4CA232CE9829 FOREIGN KEY (sub_shop_id) REFERENCES shop (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shop DROP FOREIGN KEY FK_AC6A4CA232CE9829');
        $this->addSql('DROP TABLE shop');
    }
}
