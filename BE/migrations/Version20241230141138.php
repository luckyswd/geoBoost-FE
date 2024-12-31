<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230141138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE holiday_product (id BIGINT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, holiday_name VARCHAR(255) NOT NULL, product_id BIGINT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX IDX_E19741A34D16C4DD (shop_id), INDEX idx_shop_holiday (shop_id, holiday_name), UNIQUE INDEX unique_holiday_product (holiday_name, shop_id, product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE holiday_product ADD CONSTRAINT FK_E19741A34D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday_product DROP FOREIGN KEY FK_E19741A34D16C4DD');
        $this->addSql('DROP TABLE holiday_product');
    }
}
