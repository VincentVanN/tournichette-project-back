<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812112227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, code_cart INT NOT NULL, price NUMERIC(2, 2) NOT NULL, type_cart VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, code_category INT NOT NULL, name VARCHAR(64) NOT NULL, image VARCHAR(50) DEFAULT NULL, test VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, code_depot INT NOT NULL, name VARCHAR(64) NOT NULL, address VARCHAR(100) NOT NULL, test VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, code_order INT NOT NULL, date_order DATETIME NOT NULL, price NUMERIC(2, 2) NOT NULL, payment_status VARCHAR(5) NOT NULL, deliver_status VARCHAR(5) NOT NULL, code_depot INT NOT NULL, code_user INT NOT NULL, test VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, code_product INT NOT NULL, name VARCHAR(64) NOT NULL, stock NUMERIC(3, 3) NOT NULL, unity VARCHAR(10) NOT NULL, image VARCHAR(50) NOT NULL, price NUMERIC(2, 2) NOT NULL, code_product_replace INT DEFAULT NULL, code_category INT NOT NULL, test VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cart');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE product');
    }
}
