<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701175329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competitor_product (id INT AUTO_INCREMENT NOT NULL, price_id INT DEFAULT NULL, competitor_type VARCHAR(255) NOT NULL, INDEX IDX_5CCCA57FD614C7E7 (price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prices (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, vendor_name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, fetched_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competitor_product ADD CONSTRAINT FK_5CCCA57FD614C7E7 FOREIGN KEY (price_id) REFERENCES prices (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE competitor_product DROP FOREIGN KEY FK_5CCCA57FD614C7E7');
        $this->addSql('DROP TABLE competitor_product');
        $this->addSql('DROP TABLE prices');
    }
}
