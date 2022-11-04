<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220917135545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart ADD on_sale TINYINT(1) NOT NULL, ADD archived TINYINT(1) NOT NULL, ADD archived_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE cart_product CHANGE quantity quantity NUMERIC(6, 3) NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE price price NUMERIC(7, 2) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE stock stock NUMERIC(10, 0) DEFAULT NULL, CHANGE price price NUMERIC(7, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_product CHANGE quantity quantity NUMERIC(10, 0) NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE price price NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE stock stock NUMERIC(6, 3) NOT NULL, CHANGE price price NUMERIC(5, 2) NOT NULL');
        $this->addSql('ALTER TABLE cart DROP on_sale, DROP archived, DROP archived_at');
    }
}
