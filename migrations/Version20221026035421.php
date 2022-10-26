<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221026035421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot ADD informations VARCHAR(255)');
        $this->addSql('UPDATE depot SET informations = \'Lorem ipsum dolor sit amet consectetur adipisicing elit. Praesentium cum debitis consequuntur tenetur sit sed necessitatibus alias possimus animi recusandae! Provident, quae eveniet? Nostrum consequuntur impedit est a modi distinctio?\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depot DROP informations');
    }
}
