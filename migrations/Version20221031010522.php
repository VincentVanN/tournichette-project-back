<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221031010522 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_status ADD start_mail LONGTEXT DEFAULT NULL, ADD end_mail LONGTEXT DEFAULT NULL, ADD send_mail TINYINT(1) NOT NULL, ADD start_mail_subject VARCHAR(255) DEFAULT NULL, ADD end_mail_subject VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_status DROP start_mail, DROP end_mail, DROP send_mail, DROP start_mail_subject, DROP end_mail_subject');

    }
}
