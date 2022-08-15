<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815143113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_has_products (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, quantity NUMERIC(5, 3) NOT NULL, INDEX IDX_D45332071AD5CDBF (cart_id), INDEX IDX_D45332074584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_has_cart (id INT AUTO_INCREMENT NOT NULL, orders_id INT NOT NULL, cart_id INT NOT NULL, INDEX IDX_B707B159CFFE9AD6 (orders_id), INDEX IDX_B707B1591AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_has_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, orders_id INT NOT NULL, quantity NUMERIC(5, 3) NOT NULL, INDEX IDX_A61C34044584665A (product_id), INDEX IDX_A61C3404CFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_has_products ADD CONSTRAINT FK_D45332071AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_has_products ADD CONSTRAINT FK_D45332074584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_has_cart ADD CONSTRAINT FK_B707B159CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_has_cart ADD CONSTRAINT FK_B707B1591AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE order_has_products ADD CONSTRAINT FK_A61C34044584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_has_products ADD CONSTRAINT FK_A61C3404CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA1AD5CDBF');
        $this->addSql('ALTER TABLE cart_product DROP FOREIGN KEY FK_2890CCAA4584665A');
        $this->addSql('DROP TABLE cart_product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_product (cart_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_2890CCAA1AD5CDBF (cart_id), INDEX IDX_2890CCAA4584665A (product_id), PRIMARY KEY(cart_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_product ADD CONSTRAINT FK_2890CCAA4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_has_products DROP FOREIGN KEY FK_D45332071AD5CDBF');
        $this->addSql('ALTER TABLE cart_has_products DROP FOREIGN KEY FK_D45332074584665A');
        $this->addSql('ALTER TABLE order_has_cart DROP FOREIGN KEY FK_B707B159CFFE9AD6');
        $this->addSql('ALTER TABLE order_has_cart DROP FOREIGN KEY FK_B707B1591AD5CDBF');
        $this->addSql('ALTER TABLE order_has_products DROP FOREIGN KEY FK_A61C34044584665A');
        $this->addSql('ALTER TABLE order_has_products DROP FOREIGN KEY FK_A61C3404CFFE9AD6');
        $this->addSql('DROP TABLE cart_has_products');
        $this->addSql('DROP TABLE order_has_cart');
        $this->addSql('DROP TABLE order_has_products');
    }
}
