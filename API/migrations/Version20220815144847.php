<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815144847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_has_products DROP FOREIGN KEY FK_A61C34044584665A');
        $this->addSql('ALTER TABLE order_has_products DROP FOREIGN KEY FK_A61C3404CFFE9AD6');
        $this->addSql('ALTER TABLE order_has_cart DROP FOREIGN KEY FK_B707B159CFFE9AD6');
        $this->addSql('ALTER TABLE order_has_cart DROP FOREIGN KEY FK_B707B1591AD5CDBF');
        $this->addSql('ALTER TABLE cart_has_products DROP FOREIGN KEY FK_D45332071AD5CDBF');
        $this->addSql('ALTER TABLE cart_has_products DROP FOREIGN KEY FK_D45332074584665A');
        $this->addSql('DROP TABLE order_has_products');
        $this->addSql('DROP TABLE order_has_cart');
        $this->addSql('DROP TABLE cart_has_products');
        $this->addSql('ALTER TABLE `order` ADD depot_id INT NOT NULL, ADD user_id INT NOT NULL, DROP code_order, DROP code_depot, DROP code_user, DROP test');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993988510D4DE FOREIGN KEY (depot_id) REFERENCES depot (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F52993988510D4DE ON `order` (depot_id)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_has_products (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, orders_id INT NOT NULL, quantity NUMERIC(5, 3) NOT NULL, INDEX IDX_A61C34044584665A (product_id), INDEX IDX_A61C3404CFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE order_has_cart (id INT AUTO_INCREMENT NOT NULL, orders_id INT NOT NULL, cart_id INT NOT NULL, INDEX IDX_B707B159CFFE9AD6 (orders_id), INDEX IDX_B707B1591AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cart_has_products (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, product_id INT NOT NULL, quantity NUMERIC(5, 3) NOT NULL, INDEX IDX_D45332071AD5CDBF (cart_id), INDEX IDX_D45332074584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE order_has_products ADD CONSTRAINT FK_A61C34044584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_has_products ADD CONSTRAINT FK_A61C3404CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_has_cart ADD CONSTRAINT FK_B707B159CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_has_cart ADD CONSTRAINT FK_B707B1591AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_has_products ADD CONSTRAINT FK_D45332071AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE cart_has_products ADD CONSTRAINT FK_D45332074584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993988510D4DE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP INDEX IDX_F52993988510D4DE ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('ALTER TABLE `order` ADD code_order INT NOT NULL, ADD code_depot INT NOT NULL, ADD code_user INT NOT NULL, ADD test VARCHAR(255) NOT NULL, DROP depot_id, DROP user_id');
    }
}
