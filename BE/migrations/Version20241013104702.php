<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013104702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, holiday_id INT NOT NULL, tags JSON DEFAULT NULL, INDEX IDX_389B7834D16C4DD (shop_id), INDEX IDX_389B783830A3EC0 (holiday_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B7834D16C4DD FOREIGN KEY (shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783830A3EC0 FOREIGN KEY (holiday_id) REFERENCES holiday (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B7834D16C4DD');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783830A3EC0');
        $this->addSql('DROP TABLE tag');
    }
}
