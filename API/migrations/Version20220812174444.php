<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220812174444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP code_category, DROP test, CHANGE name name VARCHAR(20) NOT NULL, CHANGE image image VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD category_id INT NOT NULL, DROP code_product, DROP code_category, DROP test');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON product (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('DROP INDEX IDX_D34A04AD12469DE2 ON product');
        $this->addSql('ALTER TABLE product ADD code_category INT NOT NULL, ADD test VARCHAR(255) NOT NULL, CHANGE category_id code_product INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD code_category INT NOT NULL, ADD test VARCHAR(255) NOT NULL, CHANGE name name VARCHAR(64) NOT NULL, CHANGE image image VARCHAR(50) DEFAULT NULL');
    }
}
