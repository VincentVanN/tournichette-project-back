<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815153942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD ordered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD payd_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP date_order');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD date_order DATETIME NOT NULL, DROP ordered_at, DROP payd_at, DROP delivered_at');
    }
}
